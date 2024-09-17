module("luci.controller.neko", package.seeall)

function index()
    entry({"admin", "services", "neko"}, firstchild(), _("NekoClash"), 1).leaf = false
    entry({"admin", "services", "neko", "neko"}, template("neko"), _("基本设置"), 2).leaf = true
    entry({"admin", "services", "neko", "mon"}, template("neko_mon"), _("订阅管理"), 3).leaf = true
    entry({"admin", "services", "neko", "yacd"}, template("neko_yacd"), _("Yacd"), 4).leaf = true
    entry({"admin", "services", "neko", "metacubexd"}, template("neko_metacubexd"), _("MetaCubeXD"), 5).leaf = true
end
