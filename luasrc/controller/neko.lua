module("luci.controller.neko", package.seeall)

function index()
    entry({"admin", "services", "neko"}, firstchild(), _("NekoClash"), 1).leaf = false
    entry({"admin", "services", "neko", "neko"}, template("neko"), _("Home"), 2).leaf = true
    entry({"admin", "services", "neko", "mon"}, template("neko_mon"), _("File Manager"), 3).leaf = true
end
