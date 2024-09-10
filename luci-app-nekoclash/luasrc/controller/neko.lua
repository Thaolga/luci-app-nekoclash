module("luci.controller.neko", package.seeall)

function index()
    entry({"admin", "services", "neko"}, firstchild(), _("NekoClash"), 13).leaf = false
    entry({"admin", "services", "neko", "neko"}, template("neko"), _("首页"), 14).leaf = true
    entry({"admin", "services", "neko", "upload"}, template("neko_upload"), _("Mihomo 文件管理"), 15).leaf = true
    entry({"admin", "services", "neko", "upload_sb"}, template("neko_upload_sb"), _("Sing-box 文件管理"), 16).leaf = true
    entry({"admin", "services", "neko", "box"}, template("neko_box"), _("Sing-box 转换模板"), 17).leaf = true
    entry({"admin", "services", "neko", "mon"}, template("neko_mon"), _("Sing-box 监控面板"), 18).leaf = true
    entry({"admin", "services", "neko", "yacd"}, template("neko_yacd"), _("Yacd"), 19).leaf = true
    entry({"admin", "services", "neko", "metacubexd"}, template("neko_metacubexd"), _("MetaCubeXD"), 20).leaf = true
end
