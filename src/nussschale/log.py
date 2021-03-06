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
    When the logger's mutex is locked no other locks can be requested.
    Thus the logger's mutex can not be part of any deadlock.
"""

from logging import ERROR, FileHandler, Formatter, INFO, Logger, getLogger
from os import mkdir, remove, rename
from os.path import isfile
from threading import RLock
from traceback import extract_tb

from nussschale.util.locks import mutex


class Log:
    """Provides logging utilities and log rotation."""

    # The logger itself
    _logger = None  # type: Logger

    def __init__(self) -> None:
        """Constructor."""
        # MutEx to enable multiple connections logging at the same time
        # Locking this MutEx can't cause any other MutExes to be locked.
        self._lock = RLock()
        # The number of logs that are kept
        self._storage = 3

    def setup(self, name: str, storage: int) -> None:
        """Setup the logger with the given name and log roll setting.

        Args:
            name: The name of the log file.
            storage: The number of log files that should be kept in the
                log directory.
        """
        self._logger = getLogger("%s_log" % name)
        self._storage = storage

        # Create the log directory
        try:
            mkdir("./logs")
        except OSError:
            pass  # Already exists

        # Setup the logger itself
        formatter = Formatter("[%(asctime)s %(levelname)s] %(message)s")
        handler = FileHandler(Log._log_roll(name, self._storage))
        handler.setFormatter(formatter)
        self._logger.addHandler(handler)
        self._logger.setLevel(INFO)

    @mutex
    def log_error(self, e: Exception, ctx: str) -> None:
        """Logs an error.

        Args:
            e: The error that occurred.
            ctx: The context where the error occurred.

        Contract:
            This method locks the logger's lock.
        """
        self.log("Error in code for %s:" % ctx)
        self.log("===[ERROR REPORT]===")
        for entry in extract_tb(e.__traceback__):
            self.log("In file %s:%i in %s: %s" % (entry.filename,
                                                  entry.lineno,
                                                  entry.name,
                                                  entry.line))
        self.log("%s was raised: %r" % (str(e), e.args))
        self.log("====[END REPORT]====")

    @mutex
    def log(self, msg: str) -> None:
        """Logs a non-critical message.

        Args:
            msg: The message that should be logged.

        Contract:
            This method locks the logger's lock.
        """
        self._logger.log(msg=msg, level=INFO)  # type: ignore

    @mutex
    def error(self, msg: str) -> None:
        """Logs a message that describes an error or a critical condition.

        Args:
            msg: The message that should be logged.

        Contract:
            This method locks the logger's lock.
        """
        self._logger.log(msg=msg, level=ERROR)  # type: ignore

    @staticmethod
    def _log_roll(keyword: str, storage: int=5) -> str:
        """Deletes and renames old log files and finds a new log file name.

        Args:
            keyword: The name of the log file.
            storage: How many log files should be kept. The default is 5.

        Returns:
            A file name that can be used for the next log file.
        """
        # Set the format for logger filenames
        fmt = "./logs/%s.%%i.log" % keyword

        # Change the name of every log file so that the first id is no longer
        # taken
        for i in range(storage)[::-1]:
            if isfile(fmt % i):
                rename(fmt % i, fmt % (i + 1))

        # Remove the last logfile if it exists
        if isfile(fmt % storage):
            remove(fmt % storage)

        # Return the final file name, with ID 0 (this name is now free)
        return fmt % 0
