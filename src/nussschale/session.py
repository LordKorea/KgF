"""Part of Nussschale.

MIT License
Copyright (c) 2017-2018 LordKorea

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
of the Software, and to permit persons to whom the Software is furnished to do
so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

Module Deadlock Guarantees:
    The session pool mutex allows no other locks to be requested and therefor
    can not be part of any deadlock.
    The session data mutex allows no other locks to be requested and therefor
    can not be part of any deadlock.
"""

from threading import RLock
from time import time
from typing import Any, Dict, Tuple
from uuid import uuid4

from nussschale.nussschale import nconfig
from nussschale.util.locks import mutex, named_mutex


class Session:
    """Represents a client session whic is kept open by a session cookie.

    Attributes:
        sid: The session ID. Should not be changed once the session
            exists.
        data: The session's data. The data object itself should not be
            overwritten.
    """

    # The MutEx for the session pool
    # Locking this MutEx can cause the Session MutEx to be locked.
    _pool_lock = RLock()

    # The session pool, all currently existing sessions
    _sessions = {}  # type: Dict[str, Session]

    @classmethod
    @named_mutex("_pool_lock")
    def add_session(cls, sid: str, session: "Session") -> None:
        """Adds a session to the session pool.

        Args:
            sid: The session ID.
            session: The session that will be stored.
        """
        Session._sessions[sid] = session

    @classmethod
    @named_mutex("_pool_lock")
    def get_session(cls, ip: str, sid: str=None) -> Tuple["Session", bool]:
        """Retrieves an existing session or creates a new one.

        A new session is created for the user if and only if at least one of
        the following conditions holds:
            - sid is None
            - sid is not in the session pool
            - a session is found but it is expired
            - a session is found but the IP addresses do not match

        Args:
            ip: The IP address of the user making the request.
            sid: The session ID provided by the user or None if none was
                provided.

        Returns:
            The session of the user and whether it was newly created.

        Contract:
            This method will lock
                - the session pool's lock
                - the session's instance lock
            in the aforementioned order and may possess both locks at the same
            time.
        """
        # Create new session if it was explicitly requested
        if sid is None:
            return Session(ip), True

        # Unknown SID -> new session
        if sid not in Session._sessions:
            return Session(ip), True

        # Fetch (maybe invalid) session
        session = Session._sessions[sid]
        create = False

        # A session expires when it was not used for some time or the
        # IP of the owner changes (basic hijacking protection)
        if session.is_expired() or not session.is_owner(ip):
            # Delete the old session and create a new one
            del Session._sessions[sid]
            session = Session(ip)
            create = True
        return session, create

    def __init__(self, ip: str) -> None:
        """Constructor.

        Args:
            ip: The IP address of the owner of the session.
        """
        # Generate a random session ID and store the session owner's IP
        self.sid = str(uuid4())
        self._ip = ip

        # Expires X minutes into the future
        self._expires = 0
        self.refresh()

        # Initialize session data
        self.data = SessionData()

        # Insert this session into the pool
        Session.add_session(self.sid, self)

    def is_expired(self) -> bool:
        """Checks whether this session is past its expiration date.

        Returns:
            Whether the session is no longer valid due to expiration.
        """
        # Locking is not needed here as access is atomic.
        return self._expires <= time()

    def is_owner(self, ip: str) -> bool:
        """Checks whether the given IP is the owner of this session.

        Args:
            ip: The IP address of the client.

        Returns:
            True if the owner has the same IP, False otherwise.
        """
        # Locking is not needed here as access is atomic.
        return self._ip == ip

    def refresh(self) -> None:
        """Refreshes the session by resetting the expiration timer."""
        # Locking is not needed here as access is atomic.
        expire_time = nconfig().get("expire_time", 15)
        self._expires = time() + expire_time * 60


class SessionData:
    """Represents session data as a thread-safe dictionary."""

    def __init__(self) -> None:
        """Constructor."""
        # The MutEx for the session data
        # Locking this MutEx can't cause any other MutExes to be locked.
        self._lock = RLock()

        # The internals of the session data
        self._internal = {}  # type: Dict[Any, Any]

    @mutex
    def remove(self, key: Any) -> None:
        """Removes the entry with the given key from the session data.

        Args:
            key: A suitable dictionary key for the entry.

        Contract:
            This method will lock the session's data lock.
        """
        del self._internal[key]

    @mutex
    def get(self, key: Any, default: Any=None) -> Any:
        """Retrieves the entry with the given key or the given default value.

        Args:
            key: The key of the entry.
            default: The default value when the key is not
                found.

        Returns:
            The entry for the given key (or the default value).

        Contract:
            This method will lock the session's data lock.
        """
        return self._internal.get(key, default)

    @mutex
    def __len__(self) -> int:
        """Retrieves the length of the data dictionary.

        Returns:
            The number of entries in the dictionary

        Contract:
            This method will lock the session's data lock.
        """
        return len(self._internal)

    @mutex
    def __getitem__(self, key: Any) -> Any:
        """Retrieves the entry for the given key.

        Args:
            key: The key of the entry to retrieve.

        Returns:
            The entry associated with the given key.

        Contract:
            This method will lock the session's data lock.
        """
        return self._internal[key]

    @mutex
    def __setitem__(self, key: Any, value: Any) -> None:
        """Sets the entry for the given key in the session data.

        Args:
            key: The key which will be used for storing the entry.
            value: The entry to store associated with the given key.

        Contract:
            This method will lock the session's data lock.
        """
        self._internal[key] = value

    @mutex
    def __contains__(self, key: Any) -> bool:
        """Checks whether an entry with the given key is present in the data.

        Args:
            key: The key to look for in the session data.

        Returns:
            Whether an entry with the given key is present.

        Contract:
            This method will lock the session's data lock.
        """
        return key in self._internal
