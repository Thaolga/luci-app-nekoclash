module("luci.controller.neko", package.seeall)
function index()
entry({"admin","services","neko"}, template("neko"), _("NekoClash"), 13).leaf=true
end
