<?php

include './cfg.php';
include './devinfo.php';
$str_cfg=substr($selected_config, strlen("$neko_dir/config")+1);
$_IMG = '/luci-static/ssr/';
if(isset($_POST['neko'])){
    $dt = $_POST['neko'];
    if ($dt == 'start') shell_exec("$neko_dir/core/neko -s");
    if ($dt == 'disable') shell_exec("$neko_dir/core/neko -k");
    if ($dt == 'restart') shell_exec("$neko_dir/core/neko -r");
    if ($dt == 'clear') shell_exec("echo \"Logs has been cleared...\" > $neko_dir/tmp/neko_log.txt");
}
$neko_status=exec("uci -q get neko.cfg.enabled");
?>

<!doctype html>
<html lang="en" data-bs-theme="<?php echo substr($neko_theme,0,-4) ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home - Neko</title>
    <link rel="icon" href="./assets/img/favicon.png">
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/custom.css" rel="stylesheet">
    <link href="./assets/theme/<?php echo $neko_theme ?>" rel="stylesheet">
    <script type="text/javascript" src="./assets/js/feather.min.js"></script>
    <script type="text/javascript" src="./assets/js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="./assets/js/neko.js"></script>
  </head>
  <body>
 
    <div class="container-sm container-bg text-center callout border border-3 rounded-4 col-11">
        <div class="row">
            <a href="#" class="col btn btn-lg">首页</a>
            <a href="./dashboard.php" class="col btn btn-lg">面板</a>
            <a href="./configs.php" class="col btn btn-lg">配置</a>
            <a href="./settings.php" class="col btn btn-lg">设定</a>
        </div>
    </div>
    <div class="container text-left p-3">
    <div class="container container-bg border border-3 rounded-4 col-12 mb-4">
    <h2 class="text-center p-2" style="margin-top: -15px; margin-bottom: 5px;">NekoClash</h2>
   <br>
<?php
$translate = [
    'United States' => '美国',
    'China' => '中国',
    'ISP' => '互联网服务提供商',
    'Japan' => '日本',
    'South Korea' => '韩国',
    'Germany' => '德国',
    'France' => '法国',
    'United Kingdom' => '英国',
    'Canada' => '加拿大',
    'Australia' => '澳大利亚',
    'Russia' => '俄罗斯',
    'India' => '印度',
    'Brazil' => '巴西',
    'Netherlands' => '荷兰',
    'Singapore' => '新加坡',
    'Hong Kong' => '香港',
    'Saudi Arabia' => '沙特阿拉伯',
    'Turkey' => '土耳其',
    'Italy' => '意大利',
    'Spain' => '西班牙',
    'Thailand' => '泰国',
    'Malaysia' => '马来西亚',
    'Indonesia' => '印度尼西亚',
    'South Africa' => '南非',
    'Mexico' => '墨西哥',
    'Israel' => '以色列',
    'Sweden' => '瑞典',
    'Switzerland' => '瑞士',
    'Norway' => '挪威',
    'Denmark' => '丹麦',
    'Belgium' => '比利时',
    'Finland' => '芬兰',
    'Poland' => '波兰',
    'Austria' => '奥地利',
    'Greece' => '希腊',
    'Portugal' => '葡萄牙',
    'Ireland' => '爱尔兰',
    'New Zealand' => '新西兰',
    'United Arab Emirates' => '阿拉伯联合酋长国',
    'Argentina' => '阿根廷',
    'Chile' => '智利',
    'Colombia' => '哥伦比亚',
    'Philippines' => '菲律宾',
    'Vietnam' => '越南',
    'Pakistan' => '巴基斯坦',
    'Egypt' => '埃及',
    'Nigeria' => '尼日利亚',
    'Kenya' => '肯尼亚',
    'Morocco' => '摩洛哥',
    'Google' => '谷歌',
    'Amazon' => '亚马逊',
    'Microsoft' => '微软',
    'Facebook' => '脸书',
    'Apple' => '苹果',
    'IBM' => 'IBM',
    'Alibaba' => '阿里巴巴',
    'Tencent' => '腾讯',
    'Baidu' => '百度',
    'Verizon' => '威瑞森',
    'AT&T' => '美国电话电报公司',
    'T-Mobile' => 'T-移动',
    'Vodafone' => '沃达丰',
    'China Telecom' => '中国电信',
    'China Unicom' => '中国联通',
    'China Mobile' => '中国移动', 
    'Chunghwa Telecom' => '中华电信',   
    'Amazon Web Services (AWS)' => '亚马逊网络服务 (AWS)',
    'Google Cloud Platform (GCP)' => '谷歌云平台 (GCP)',
    'Microsoft Azure' => '微软Azure',
    'Oracle Cloud' => '甲骨文云',
    'Alibaba Cloud' => '阿里云',
    'Tencent Cloud' => '腾讯云',
    'DigitalOcean' => '数字海洋',
    'Linode' => '林诺德',
    'OVHcloud' => 'OVH 云',
    'Hetzner' => '赫兹纳',
    'Vultr' => '沃尔特',
    'OVH' => 'OVH',
    'DreamHost' => '梦想主机',
    'InMotion Hosting' => '动态主机',
    'HostGator' => '主机鳄鱼',
    'Bluehost' => '蓝主机',
    'A2 Hosting' => 'A2主机',
    'SiteGround' => '站点地',
    'Liquid Web' => '液态网络',
    'Kamatera' => '卡玛特拉',
    'IONOS' => 'IONOS',
    'InterServer' => '互联服务器',
    'Hostwinds' => '主机之风',
    'ScalaHosting' => '斯卡拉主机',
    'GreenGeeks' => '绿色极客'
];
$lang = $_GET['lang'] ?? 'en';
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-dns-prefetch-control" content="on">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//whois.pconline.com.cn">
    <link rel="dns-prefetch" href="//forge.speedtest.cn">
    <link rel="dns-prefetch" href="//api-ipv4.ip.sb">
    <link rel="dns-prefetch" href="//api.ipify.org">
    <link rel="dns-prefetch" href="//api.ttt.sh">
    <link rel="dns-prefetch" href="//qqwry.api.skk.moe">
    <link rel="dns-prefetch" href="//d.skk.moe">
    <link rel="preconnect" href="https://forge.speedtest.cn">
    <link rel="preconnect" href="https://whois.pconline.com.cn">
    <link rel="preconnect" href="https://api-ipv4.ip.sb">
    <link rel="preconnect" href="https://api.ipify.org">
    <link rel="preconnect" href="https://api.ttt.sh">
    <link rel="preconnect" href="https://qqwry.api.skk.moe">
    <link rel="preconnect" href="https://d.skk.moe">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
        }
        .status {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: left;
            flex-direction: row;
            height: 50px;
            letter-spacing: 0.5px;
        }
        .img-con {
            margin-right: 3rem;
        }
        .img-con img {
            width: 80px;
            height: auto;
        }
        .block {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .ip-address {
            color: #2dce89;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 0;
        }
        .info {
            color: #fb6340;
            font-style: italic;
            font-size: 1rem;
            margin: 0;
        }
    </style>
</head>
<body>
<?php if (in_array($lang, ['zh-cn', 'en', 'auto'])): ?>
    <fieldset class="cbi-section">
        <div class="status">
            <div class="img-con">
                <img src="/nekoclash/assets/neko/img/loading.svg" id="flag" class="pure-img" title="国旗">
            </div>
            <div class="block">
                <p id="d-ip" class="green ip-address">Checking...</p>
                <p id="ipip" class="info"></p>
            </div>
        </div>
    </fieldset>
<?php endif; ?>

<script src="/nekoclash/assets/neko/js/jquery.min.js"></script>
<script type="text/javascript">
    const _IMG = '/nekoclash/assets/neko/';
    const translate = <?php echo json_encode($translate, JSON_UNESCAPED_UNICODE); ?>;
    let cachedIP = null;
    let cachedInfo = null;
    let random = parseInt(Math.random() * 100000000);

    let IP = {
        get: (url, type) =>
            fetch(url, { method: 'GET' }).then((resp) => {
                if (type === 'text')
                    return Promise.all([resp.ok, resp.status, resp.text(), resp.headers]);
                else
                    return Promise.all([resp.ok, resp.status, resp.json(), resp.headers]);
            }).then(([ok, status, data, headers]) => {
                if (ok) {
                    return { ok, status, data, headers };
                } else {
                    throw new Error(JSON.stringify(data.error));
                }
            }).catch(error => {
                console.error("Error fetching data:", error);
                throw error;
            }),
        Ipip: (ip, elID) => {
            if (ip === cachedIP && cachedInfo) {
                console.log("Using cached IP info");
                IP.updateUI(cachedInfo, elID);
            } else {
                IP.get(`https://api.ip.sb/geoip/${ip}`, 'json')
                    .then(resp => {
                        cachedIP = ip;  
                        cachedInfo = resp.data;  
                        IP.updateUI(resp.data, elID);
                    })
                    .catch(error => {
                        console.error("Error in Ipip function:", error);
                    });
            }
        },
        updateUI: (data, elID) => {
            let country = translate[data.country] || data.country;
            let isp = translate[data.isp] || data.isp;
            let asnOrganization = translate[data.asn_organization] || data.asn_organization;

            if (data.country === 'Taiwan') {
                country = (navigator.language === 'en') ? 'China Taiwan' : '中国台湾';
            }

            document.getElementById(elID).innerHTML = `${country} ${isp} ${asnOrganization}`;
            $("#flag").attr("src", _IMG + "flags/" + data.country + ".png");
            document.getElementById(elID).style.color = '#FF00FF';
        },
        getIpipnetIP: () => {
            if (cachedIP) {
                document.getElementById('d-ip').innerHTML = cachedIP;
                IP.updateUI(cachedInfo, 'ipip');
            } else {
                IP.get(`https://api.ipify.org?format=json&z=${random}`, 'json')
                    .then((resp) => {
                        let ip = resp.data.ip;
                        cachedIP = ip; 
                        document.getElementById('d-ip').innerHTML = ip;
                        return ip;
                    })
                    .then(ip => {
                        IP.Ipip(ip, 'ipip');
                    })
                    .catch(error => {
                        console.error("Error in getIpipnetIP function:", error);
                    });
            }
        }
    }

    IP.getIpipnetIP();
    setInterval(IP.getIpipnetIP, 5000);
</script>
</body>
</html>
<tbody>
    <tr>
   <br>
<?php
date_default_timezone_set('Asia/Shanghai'); 
$singbox_status = 0;

$logDir = '/etc/neko/tmp/';
$logFile = $logDir . 'log.txt'; 
$singBoxLogFile = '/var/log/singbox_log.txt'; 
$singboxStartLogFile = $logDir . 'singbox_start_log.txt'; 

$singBoxPath = '/usr/bin/sing-box';
$configDir = '/etc/neko/config'; 

$start_script_template = <<<EOF
#!/bin/bash

exec >> $logFile 2>&1  
exec 2>> $singBoxLogFile  

if command -v fw4 > /dev/null; then
    echo "FW4 Detected."
    echo "Starting nftables."

    echo '#!/usr/sbin/nft -f

flush ruleset

table inet singbox {
  set local_ipv4 {
    type ipv4_addr
    flags interval
    elements = {
      10.0.0.0/8,
      127.0.0.0/8,
      169.254.0.0/16,
      172.16.0.0/12,
      192.168.0.0/16,
      240.0.0.0/4
    }
  }

  set local_ipv6 {
    type ipv6_addr
    flags interval
    elements = {
      ::ffff:0.0.0.0/96,
      64:ff9b::/96,
      100::/64,
      2001::/32,
      2001:10::/28,
      2001:20::/28,
      2001:db8::/32,
      2002::/16,
      fc00::/7,
      fe80::/10
    }
  }

  chain singbox-tproxy {
    fib daddr type { unspec, local, anycast, multicast } return
    ip daddr @local_ipv4 return
    ip6 daddr @local_ipv6 return
    udp dport { 123 } return
    meta l4proto { tcp, udp } meta mark set 1 tproxy to :9888 accept
  }

  chain singbox-mark {
    fib daddr type { unspec, local, anycast, multicast } return
    ip daddr @local_ipv4 return
    ip6 daddr @local_ipv6 return
    udp dport { 123 } return
    meta mark set 1
  }

  chain mangle-output {
    type route hook output priority mangle; policy accept;
    meta l4proto { tcp, udp } skgid != 1 ct direction original goto singbox-mark
  }

  chain mangle-prerouting {
    type filter hook prerouting priority mangle; policy accept;
    iifname { lo, eth0 } meta l4proto { tcp, udp } ct direction original goto singbox-tproxy
  }
  }' > /etc/nftables.conf

    nft -f /etc/nftables.conf

    elif command -v fw3 > /dev/null; then
    echo "FW3 Detected."
    echo "Starting iptables."

    iptables -t mangle -F
    iptables -t mangle -X

    iptables -t mangle -N singbox-mark
    iptables -t mangle -A singbox-mark -m addrtype --dst-type UNSPEC,LOCAL,ANYCAST,MULTICAST -j RETURN
    iptables -t mangle -A singbox-mark -d 10.0.0.0/8 -j RETURN
    iptables -t mangle -A singbox-mark -d 127.0.0.0/8 -j RETURN
    iptables -t mangle -A singbox-mark -d 169.254.0.0/16 -j RETURN
    iptables -t mangle -A singbox-mark -d 172.16.0.0/12 -j RETURN
    iptables -t mangle -A singbox-mark -d 192.168.0.0/16 -j RETURN
    iptables -t mangle -A singbox-mark -d 240.0.0.0/4 -j RETURN
    iptables -t mangle -A singbox-mark -p udp --dport 123 -j RETURN
    iptables -t mangle -A singbox-mark -j MARK --set-mark 1

    iptables -t mangle -N singbox-tproxy
    iptables -t mangle -A singbox-tproxy -m addrtype --dst-type UNSPEC,LOCAL,ANYCAST,MULTICAST -j RETURN
    iptables -t mangle -A singbox-tproxy -d 10.0.0.0/8 -j RETURN
    iptables -t mangle -A singbox-tproxy -d 127.0.0.0/8 -j RETURN
    iptables -t mangle -A singbox-tproxy -d 169.254.0.0/16 -j RETURN
    iptables -t mangle -A singbox-tproxy -d 172.16.0.0/12 -j RETURN
    iptables -t mangle -A singbox-tproxy -d 192.168.0.0/16 -j RETURN
    iptables -t mangle -A singbox-tproxy -d 240.0.0.0/4 -j RETURN
    iptables -t mangle -A singbox-tproxy -p udp --dport 123 -j RETURN
    iptables -t mangle -A singbox-tproxy -p tcp -j TPROXY --tproxy-mark 0x1/0x1 --on-port 9888
    iptables -t mangle -A singbox-tproxy -p udp -j TPROXY --tproxy-mark 0x1/0x1 --on-port 9888

    iptables -t mangle -A OUTPUT -p tcp -m cgroup ! --cgroup 1 -j singbox-mark
    iptables -t mangle -A OUTPUT -p udp -m cgroup ! --cgroup 1 -j singbox-mark
    iptables -t mangle -A PREROUTING -i lo -p tcp -j singbox-tproxy
    iptables -t mangle -A PREROUTING -i lo -p udp -j singbox-tproxy
    iptables -t mangle -A PREROUTING -i eth0 -p tcp -j singbox-tproxy
    iptables -t mangle -A PREROUTING -i eth0 -p udp -j singbox-tproxy

else
    echo "Neither fw3 nor fw4 detected, unable to configure firewall rules."
    exit 1
fi

echo "Configs : %s"
exec >> $singBoxLogFile 2>&1  
/usr/bin/sing-box run -c %s
EOF;

$maxFileSize = 1024 * 1024 * 5; 
$maxBackupFiles = 5; 

function getAvailableConfigFiles() {
    global $configDir;
    return glob("$configDir/*.json");
}

function createStartScript($configFile) {
    global $start_script_template;
    $start_script = sprintf($start_script_template, $configFile, $configFile);
    file_put_contents('/etc/neko/core/start.sh', $start_script);
    chmod('/etc/neko/core/start.sh', 0755);
}

function createRestartFirewallScript() {
    $scriptPath = '/etc/neko/core/restart_firewall.sh';
    $scriptContent = <<<EOF
#!/bin/bash

service firewall restart
if [ \$? -eq 0 ]; then
    echo "[\$(date '+%H:%M:%S')] Restarting Firewall." >> /etc/neko/tmp/log.txt
else
    echo "[\$(date '+%H:%M:%S')] Firewall restart failed." >> /etc/neko/tmp/log.txt
fi
EOF;

    $result = file_put_contents($scriptPath, $scriptContent);
    if ($result === false) {
        error_log("Failed to create script at $scriptPath");
    } else {
        chmod($scriptPath, 0755);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['singbox'])) {
        if ($_POST['singbox'] === 'start') {
            createRestartFirewallScript(); 
      }
    }
}


function rotateLogFile($filePath) {
    $backupPath = $filePath . '-' . date('Y-m-d-H-i-s') . '.bak';
    rename($filePath, $backupPath);  
    touch($filePath);  
    cleanUpOldBackups(dirname($filePath), basename($filePath));
}

function cleanUpOldBackups($dir, $fileName) {
    global $maxBackupFiles;
    $pattern = preg_quote($fileName, '/');
    $files = glob("$dir/$pattern-*.bak");

    if (count($files) > $maxBackupFiles) {
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $filesToDelete = array_slice($files, $maxBackupFiles);
        foreach ($filesToDelete as $file) {
            unlink($file);
        }
    }
}

function checkLogFileSize($filePath, $maxFileSize) {
    if (file_exists($filePath)) {
        $fileSize = filesize($filePath);
        if ($fileSize > $maxFileSize) {
            rotateLogFile($filePath);
        }
    }
}

function isSingboxRunning() {
    global $singBoxPath;
    $command = "ps w | grep '$singBoxPath' | grep -v grep";
    exec($command, $output);
    return !empty($output);
}

function getRunningConfigFile() {
    global $singBoxPath;
    $command = "ps w | grep '$singBoxPath' | grep -v grep";
    exec($command, $output);
    foreach ($output as $line) {
        if (strpos($line, '-c') !== false) {
            $parts = explode('-c', $line);
            if (isset($parts[1])) {
                $configPath = trim(explode(' ', trim($parts[1]))[0]);
                return $configPath;
            }
        }
    }
    return null;
}

if (isSingboxRunning()) {
    $singbox_status = 1; 
} else {
    $singbox_status = 0; 
}

if ($singbox_status == 1) {
    $runningConfigFile = getRunningConfigFile();
    if ($runningConfigFile) {
        $str_cfg = htmlspecialchars(basename($runningConfigFile));
    } else {
        $str_cfg = 'Sing-box 配置文件：未找到运行中的配置文件';
    }
}

function getSingboxVersion() {
    global $singBoxPath;
    $command = "$singBoxPath version 2>&1";
    exec($command, $output, $returnVar);
    if ($returnVar === 0) {
        foreach ($output as $line) {
            if (strpos($line, 'version') !== false) {
                return trim(substr($line, strpos($line, 'version') + 8)); 
            }
        }
    }
    return 'Unknown version';
}

function getSingboxPID() {
    global $singBoxPath;
    $command = "ps w | grep '$singBoxPath' | grep -v grep | awk '{print \$1}'";
    exec($command, $output);
    return isset($output[0]) ? $output[0] : null;
}

function stopSingbox() {
    $pid = getSingboxPID();
    if ($pid) {
        exec("kill -15 $pid", $output, $returnVar);
        if ($returnVar !== 0) {
            exec("kill -9 $pid", $output, $returnVar);
        }
        exec('/etc/neko/core/restart_firewall.sh', $output, $returnVar);

        if ($returnVar === 0) {
            logToFile('/etc/neko/tmp/log.txt', "Restarting Firewall.");
            return true;
        } else {
            logToFile('/etc/neko/tmp/log.txt', "Firewall restart failed.");
            error_log("Failed to stop Sing-box with PID $pid");
        }
    }
    return false;
}


function logToFile($filePath, $message) {
    $timestamp = date('H:i:s');
    file_put_contents($filePath, "[$timestamp] $message\n", FILE_APPEND);
}

function applyFirewallRules() {
    global $nftables_rules;
    file_put_contents('/etc/nftables.conf', $nftables_rules);
    exec('nft -f /etc/nftables.conf');
}

function readRecentLogLines($filePath, $lines = 1000) {
    $command = "tail -n $lines " . escapeshellarg($filePath);
    return shell_exec($command);
}

$availableConfigs = getAvailableConfigFiles();
$currentConfigFile = isset($_POST['config_file']) ? $_POST['config_file'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['config_file']) && file_exists($_POST['config_file'])) {
        $configFile = $_POST['config_file'];  

        if ($_POST['singbox'] === 'start') {
            checkLogFileSize($singBoxLogFile, $maxFileSize);
            applyFirewallRules();
            createStartScript($configFile); 
            exec("/etc/neko/core/start.sh > $singBoxLogFile 2>&1 &", $output, $returnVar);
            $version = getSingboxVersion();
            $pid = getSingboxPID();
            $currentTimestamp = date('H:i:s'); 
            $logMessage = $returnVar === 0 
                ? "Sing-box started\n[$currentTimestamp] Core Detected : $version" 
                 : "Failed to start Sing-box\n[$currentTimestamp]";
            logToFile($logFile, $logMessage); 
            $singbox_status = $returnVar === 0 ? 1 : 0;
        } elseif ($_POST['singbox'] === 'disable') {
            $success = stopSingbox();
            $logMessage = $success ? "Sing-box has been stopped" : "Failed to stop Sing-box";
            logToFile($logFile, $logMessage); 
            $singbox_status = $success ? 0 : $singbox_status;
        } elseif ($_POST['singbox'] === 'restart') {
            $success = stopSingbox();
            if ($success) {
                checkLogFileSize($singBoxLogFile, $maxFileSize); 
                applyFirewallRules();
                createStartScript($configFile);
                exec("/etc/neko/core/start.sh > $singBoxLogFile 2>&1 &", $output, $returnVar);
                exec('/etc/neko/core/restart_firewall.sh', $output, $returnVar); 
                $version = getSingboxVersion();
                $pid = getSingboxPID();
                $logMessage = $returnVar === 0 
                    ? "Sing-box has been restarted，version: $version, PID: $pid" 
                    : "Failed to start Sing-box";    
                logToFile($logFile, $logMessage); 
                $singbox_status = $returnVar === 0 ? 1 : 0;
            } else {
                logToFile($logFile, "Failed to stop Sing-box"); 
            }
        }
    }

    if (isset($_POST['clear_singbox_log'])) {
        file_put_contents($singBoxLogFile, ''); 
        $message = 'Sing-box运行日志已清空';
    }

    if (isset($_POST['clear_plugin_log'])) {
        file_put_contents($logFile, ''); 
        $message = '插件日志已清空';
    }
}

function readLogFile($filePath) {
    if (file_exists($filePath)) {
        return nl2br(htmlspecialchars(readRecentLogLines($filePath, 1000), ENT_NOQUOTES));
    } else {
        return '日志文件不存在。';
    }
}

$logContent = readLogFile($logFile); 
$singboxLogContent = readLogFile($singBoxLogFile); 
$singboxStartLogContent = readLogFile($singboxStartLogFile); 
?>
    <table class="table table-borderless  mb-2">
        <tbody>
            <tr>
    <style>
        .btn-group .btn {
            width: 100%; 
        }
    </style>
<table class="table">
    <tbody>
        <tr>
<td class="align-middle">状态</td>
<td>
    <div class="d-flex flex-wrap gap-2">
        <?php if($neko_status==1): ?>
            <span class="badge bg-success" style="font-size: 16px; padding: 8px 12px;">Mihomo 运行中</span>
        <?php else: ?>
            <span class="badge bg-danger" style="font-size: 16px; padding: 8px 12px;">Mihomo 未运行</span>
        <?php endif; ?>
        <span class="badge bg-info " style="font-size: 16px; padding: 8px 12px;"><?php echo $str_cfg; ?></span>
        <?php if ($singbox_status == 1): ?>
            <span class="badge bg-success" style="font-size: 16px; padding: 8px 12px;">Sing-box 运行中</span>
        <?php else: ?>
            <span class="badge bg-danger" style="font-size: 16px; padding: 8px 12px;">Sing-box 未运行</span>
        <?php endif; ?>
                </div>
            </td>
        </tr>
        <tr>
       <tr>
            <td class="align-middle">控制</td>
            <td>
                <div class="row g-2">
                    <div class="col-md-6">
                        <form action="index.php" method="post">
                            <div class="mb-2">
                                <div style="height: 31px;">       <h5 class="text-center p-2">选择配置文件</h5></div>
                            </div>
                            <div class="d-grid gap-2">
                    <button type="submit" name="neko" value="start" class="btn <?php echo $neko_status == 1 ? 'btn-outline-success' : 'btn-success'; ?>" style="font-size: 16px; <?php echo $neko_status == 1 ? 'opacity: 0.6;' : ''; ?>">启用 Mihomo</button>
                    <button type="submit" name="neko" value="disable" class="btn <?php echo $neko_status == 0 ? 'btn-outline-danger' : 'btn-danger'; ?>" style="font-size: 16px; <?php echo $neko_status == 0 ? 'opacity: 0.6;' : ''; ?>">停用 Mihomo</button>
                    <button type="submit" name="neko" value="restart" class="btn <?php echo $neko_status == 0 ? 'btn-outline-warning' : 'btn-warning'; ?>" style="font-size: 16px; <?php echo $neko_status == 0 ? 'opacity: 0.6;' : ''; ?>">重启 Mihomo</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form action="index.php" method="post">
                            <div class="mb-2">
                                <select name="config_file" id="config_file" class="form-select form-select-sm">
                                    <?php foreach ($availableConfigs as $config): ?>
                                        <option value="<?= htmlspecialchars($config) ?>" <?= isset($_POST['config_file']) && $_POST['config_file'] === $config ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(basename($config)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="d-grid gap-2">
                    <button type="submit" name="singbox" value="start" class="btn <?php echo $singbox_status == 1 ? 'btn-outline-info' : 'btn-success'; ?>" style="font-size: 16px; <?php echo $singbox_status == 1 ? 'opacity: 0.6;' : ''; ?>">启用 Sing-box</button>
                    <button type="submit" name="singbox" value="disable" class="btn <?php echo $singbox_status == 0 ? 'btn-outline-danger' : 'btn-danger'; ?>" style="font-size: 16px; <?php echo $singbox_status == 0 ? 'opacity: 0.6;' : ''; ?>">停用 Sing-box</button>
                    <button type="submit" name="singbox" value="restart" class="btn <?php echo $singbox_status == 0 ? 'btn-outline-warning' : 'btn-warning'; ?>" style="font-size: 16px; <?php echo $singbox_status == 0 ? 'opacity: 0.6;' : ''; ?>">重启 Sing-box</button>
                            </div>
                        </form>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="align-middle">运行模式</td>
            <td>
                <?php
                $mode_placeholder = '';
                if ($neko_status == 1) {
                    $mode_placeholder = $neko_cfg['echanced'] . " | " . $neko_cfg['mode'];
                } elseif ($singbox_status == 1) {
                    $mode_placeholder = "Rule 模式";
                } else {
                    $mode_placeholder = "未运行";
                }
                ?>
                <input class="form-control form-control-sm text-center" name="mode" type="text" value="<?php echo $mode_placeholder; ?>" readonly>
            </td>
        </tr>
    </tbody>
</table>
    <h2 class="text-center p-2" >系统信息</h2>
    <table class="table table-borderless rounded-4 mb-2">
        <tbody>
            <tr>
                <td>型号</td>
                <td class="col-7"><?php echo $devices ?></td>
            </tr>
            <tr>
                <td>内存</td>
                <td class="col-7"><?php echo "$ramUsage/$ramTotal MB" ?></td>
            </tr>
            <tr>
                <td>固件版本</td>
                <td class="col-7"><?php echo $OSVer ?></td>
            </tr>
            <tr>
                <td>内核版本</td>
                <td class="col-7"><?php echo $kernelv ?></td>
            </tr>
            <tr>
                <td>平均负载</td>
                <td class="col-7"><?php echo "$cpuLoadAvg1Min $cpuLoadAvg5Min $cpuLoadAvg15Min" ?></td>
            </tr>
            <tr>
                <td>运行时间</td>
                <td class="col-7"><?php echo "{$days}天 {$hours}小时 {$minutes}分钟 {$seconds}秒" ?></td>
            </tr>
        </tbody>
    </table>
  <br>
<div style="border: 1px solid black; padding: 10px; text-align: center;">
    <table style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 50%;">下载-总计</td>
                <td style="width: 50%;">上传-总计</td>
            </tr>
            <tr>
                <td><span id="downtotal">-</span></td>
                <td><span id="uptotal">-</span></td>
            </tr>
        </tbody>
    </table>
</div>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            padding: 20px;
        }
        .log-section {
            border: 2px solid #c0c0c0; 
            padding: 10px; 
            border-radius: 8px;
            flex: 1;
            min-width: 0; 
            margin-bottom: 20px; 
        }
        .log-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .log-header {
            text-align: center;
            margin-bottom: 10px;
            font-size: 1.1rem; 
        }
        pre.form-control {
            height: 300px; 
            width: 100%; 
            padding: 10px;
            box-sizing: border-box;
            white-space: pre-wrap; 
            overflow-x: hidden; 
            overflow-y: auto; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
        }
        .log-footer {
            display: flex;
            justify-content: center; 
            margin-top: auto;
        }
        .nav-buttons {
            display: flex;
            justify-content: center;
            gap: 10px; 
            margin-top: 20px;
        }
        .nav-buttons a {
            display: inline-block;
            text-decoration: none;
            color: #ffffff;
            border: 1px solid;
            border-radius: 4px;
            padding: 10px 20px;
            text-align: center;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-buttons a:hover {
            opacity: 0.9;
        }
        .current-menu-button {
            background-color: #007bff; 
            border-color: #007bff;
        }
        .current-menu-button:hover {
            background-color: #0056b3; 
            border-color: #004085;
        }
        .config-menu-button {
            background-color: #28a745; 
            border-color: #28a745;
        }
        .config-menu-button:hover {
            background-color: #218838; 
            border-color: #1e7e34;
        }
        .monitoring-button {
            background-color: #ffc107; 
            border-color: #ffc107;
        }
        .monitoring-button:hover {
            background-color: #e0a800; 
            border-color: #d39e00;
        }
        .box-menu-button {
            background-color: #ff69b4; 
            border-color: #ff1493;     
            color: white;              
        }
        .box-menu-button:hover {
            background-color: #ff69b4; 
            border-color: #ff1493;    
        }
        .main-menu-button {
            background-color: #dc3545; 
            border-color: #dc3545;
        }
        .main-menu-button:hover {
            background-color: #c82333; 
            border-color: #bd2130;
        }
        footer {
            text-align: center;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .log-section {
                margin-bottom: 20px; 
                margin-right: 0; 
                flex: 1 0 100%; 
            }
            .d-flex {
                flex-direction: column; 
            }
        }
    </style>
</head>
<body>
   <div class="container rounded-4 col-12 mb-4">
        <h2 class="text-center p-2">日志</h2>
        <div class="d-flex flex-wrap">
            <div class="log-section">
                <div class="log-container">
                    <h4 class="log-header">NeKo 日志</h4>
                    <pre id="plugin_log" class="form-control" style="overflow-y: scroll;"></pre>
                    <form action="index.php" method="post" class="mt-3 log-footer">
                        <button type="submit" name="clear_plugin_log" class="btn btn-danger btn-clear-log">清空日志</button>
                    </form>
                </div>
            </div>

            <div class="log-section">
                <div class="log-container">
                    <h4 class="log-header">Mihomo 日志</h4>
                    <pre id="bin_logs" class="form-control" style="overflow-y: scroll;"></pre> 
                    <form action="index.php" method="post" class="mt-3 log-footer">
                        <button type="submit" name="neko" value="clear" class="btn btn-danger btn-clear-log">清空日志</button>
                    </form>
                </div>
            </div>

            <div class="log-section">
                <div class="log-container">
                    <h4 class="log-header">Sing-box 日志</h4>
                    <pre id="singbox_log" class="form-control" style="overflow-y: scroll;"></pre>
                    <form action="index.php" method="post" class="mt-3 log-footer">
                        <button type="submit" name="clear_singbox_log" class="btn btn-danger btn-clear-log">清空日志</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function scrollToBottom(elementId) {
            var logElement = document.getElementById(elementId);
            logElement.scrollTop = logElement.scrollHeight; 
        }

        function fetchLogs() {
            Promise.all([
                fetch('fetch_logs.php?file=plugin_log'),  
                fetch('fetch_logs.php?file=mihomo_log'),  
                fetch('fetch_logs.php?file=singbox_log')  
            ])
            .then(responses => Promise.all(responses.map(res => res.text()))) 
            .then(data => {
                document.getElementById('plugin_log').textContent = data[0]; 
                document.getElementById('bin_logs').textContent = data[1];   
                document.getElementById('singbox_log').textContent = data[2]; 

                scrollToBottom('plugin_log');
                scrollToBottom('bin_logs');   
                scrollToBottom('singbox_log');
            })
            .catch(err => console.error('Error fetching logs:', err));
        }

        fetchLogs();
        setInterval(fetchLogs, 5000);
    </script>
<a href="/nekoclash/mon.php" class="config-menu-button d-flex justify-content-center align-items-center" 
   style="height: 40px; width: 40px; line-height: 40px; border-radius: 50%; background-color: transparent; border: 5px solid; color: #ffcc00; position: absolute; top: 10px; left: 20px; text-align: center; text-decoration: none; transition: opacity 0.3s; animation: borderAnimation 3s linear infinite;" 
   onclick="speakAndNavigate('打开Mihomo 管理面板', '/nekoclash/mon.php'); return false;"
   onmouseover="this.style.opacity='0.8';" onmouseout="this.style.opacity='1';">
<i>⚙️</i>  
</a>
    <script>
        function speakAndNavigate(message, url) {
            speakMessage(message);
            setTimeout(function() {
                window.location.href = url;
            }, 500); 
        }
    </script>
    <footer class="text-center">
        <p><?php echo isset($message) ? $message : ''; ?></p>
        <p><?php echo $footer; ?></p>
    </footer>
</body>
</html>
