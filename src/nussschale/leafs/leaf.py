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
"""

from nussschale.leafs.master import MasterController
from pages.api import APIController
from pages.dashboard import DashboardController
from pages.deckedit import DeckeditController
from pages.index import IndexController
from pages.match import MatchController
from pages.options import OptionsController
from pages.resource import ResourceController


class Leafs:  # todo
    """The registry where controllers for leafs can be added."""

    @staticmethod
    def add_leafs(mctrl: MasterController):
        """Registers all leafs in the given master controller.

        To register a leaf, add a line of the form
            mctrl.add_leaf("test", TestController())

        Args:
            mctrl: The master controller to put the leafs into.
        """
        # API controller, for interaction with matches and the game itself
        mctrl.add_leaf("api", APIController())
        # Dashboard controller, the dashboard with the match view
        mctrl.add_leaf("dashboard", DashboardController())
        # Deckedit controller, the deck editor
        mctrl.add_leaf("deckedit", DeckeditController())
        # Index controller, the default landing page
        mctrl.add_leaf("index", IndexController())
        # Match controller, for creating/joining and the match view
        mctrl.add_leaf("match", MatchController())
        # Options controller, API for user options
        mctrl.add_leaf("options", OptionsController())
        # Resource loader for css/js/images
        mctrl.add_leaf("res", ResourceController())
