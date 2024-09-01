<h1 align="center">
  <img src="https://raw.githubusercontent.com/Thaolga/neko/main/img/neko.png" alt="neko" width="500">
</h1>

<div align="center">
 <a target="_blank" href="https://github.com/Thaolga/luci-app-nekoclash/releases"><img src="https://img.shields.io/github/downloads/nosignals/neko/total?label=Total%20Download&labelColor=blue&style=for-the-badge"></a>
 <a target="_blank" href="https://dbai.team/discord"><img src="https://img.shields.io/discord/1127928183824597032?style=for-the-badge&logo=discord&label=%20"></a>
</div>


<p align="center">
  XRAY/V2ray, Shadowsocks, ShadowsocksR, etc.</br>
  Mihomo based Proxy
</p>

# Project Update Announcement: Sing-box Support Introduced Starting from Version 1.1.33
---
## We are pleased to announce that starting from version 1.1.33, the project has fully integrated support for Sing-box. Sing-box requires collaboration with firewall management features, specifically firewall4 + nftables. This update significantly enhances the system's flexibility and security, allowing users to exercise more precise control over traffic management policies.

- Sing-box Support: Sing-box has been integrated and requires the use of firewall4 + nftables, offering you a smarter and more efficient traffic management solution.
- Introducing an intelligent conversion template to completely solve the configuration difficulties of Sing-box. Our goal is to enable zero-threshold use of Sing-box.

## It is important to note that although this version greatly enhances system capabilities, the original Mihomo-related features remain unchanged, ensuring that users can continue to enjoy a stable service experience.

Depedencies
---
- Mihomo
  - ` php8 `
  - ` php8-cgi `
  - ` php8-mod-curl`
  - ` firewall `
  - ` iptables `
   
- Sing-box
  - ` php8 `
  - ` php8-cgi `
  - ` php8-mod-curl`
  - ` firewall4 `
  - ` kmod-tun `
  - ` xtables-nft `

# OpenWrt One-Click Installation Script
---

```bash
wget -O /root/nekoclash.sh https://cdn.jsdelivr.net/gh/Thaolga/luci-app-nekoclash@main/nekoclash.sh && chmod 0755 /root/nekoclash.sh && /root/nekoclash.sh

```

# OpenWrt Compilation
---
## Cloning the Source Code:
---

```bash
git clone https://github.com/Thaolga/luci-app-nekoclash  package/luci-app-nekoclash

```

## Compile :
---

```bash
make package/luci-app-nekoclash/{clean,compile} V=s
```
# Screenshoot
---
<details><summary>Home</summary>
 <p>
 <img src="https://raw.githubusercontent.com/Thaolga/neko/main/img/ge.png" alt="home" >
 </p>
</details>

 <details><summary>Dasboard</summary>
 <p>
 <img src="https://raw.githubusercontent.com/Thaolga/neko/main/img/im.png" >
 <img src="https://raw.githubusercontent.com/Thaolga/neko/main/img/image_2024-08-30_14-56-43.png" >
 <img src="https://raw.githubusercontent.com/Thaolga/neko/main/img/image_2024-08-30_14-54-51.png" >
 <img src="https://raw.githubusercontent.com/Thaolga/neko/main/img/image_2024-08-30_14-38-43.png" >
 <img src="https://raw.githubusercontent.com/Thaolga/neko/main/img/image_2024-08-30_14-44-25.png" >
 <img src="https://raw.githubusercontent.com/Thaolga/neko/main/img/image_2024-08-30_14-43-50.png" >
 <img src="https://raw.githubusercontent.com/Thaolga/neko/main/img/image_2024-08-30_14-43-18.png" >
 <img src="https://raw.githubusercontent.com/Thaolga/neko/main/img/image_2024-08-30_14-42-30.png" >
 <img src="https://raw.githubusercontent.com/Thaolga/neko/main/img/image_2024-08-30_14-45-28.png" >
 </p>
</details>
