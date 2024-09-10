<?php

include './cfg.php';
include './devinfo.php';
include './video.php';
$str_cfg=substr($selected_config, strlen("$neko_dir/config")+1);
$_IMG = '/luci-static/ssr/';
if(isset($_POST['neko'])){
    $dt = $_POST['neko'];
    if ($dt == 'start') shell_exec("$neko_dir/core/neko -s");
    if ($dt == 'disable') shell_exec("$neko_dir/core/neko -k");
    if ($dt == 'restart') shell_exec("$neko_dir/core/neko -r");
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
            <a href="#" class="col btn btn-lg">Home</a>
            <a href="./dashboard.php" class="col btn btn-lg">Dashboard</a>
            <a href="./configs.php" class="col btn btn-lg">Configs</a>
            <a href="./settings.php" class="col btn btn-lg">Settings</a>
        </div>
    </div>
    <div class="container text-left p-3">
       
        <div class="container container-bg border border-3 rounded-4 col-12 mb-4">
    <h2 class="text-center p-2">Running Status</h2>
    <table class="table table-borderless mb-2">
        <div class="container container-bg border border-3 rounded-4 col-12 mb-4">
   <br>
<?php
$translate = [

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
        .status {
            display: flex;
            align-items: center; 
            justify-content: center; 
            text-align: center; 
            flex-direction: column; 
        }

        .img-con {
            margin-bottom: 1rem; 
        }

        .img-con img {
            width: 65px; 
            height: auto; 
        }

        .green {
            font-size: .9rem; 
            color: #2dce89; 
        }

        .red {
            font-size: .9rem; 
            color: #fb6340; 
        }

        .yellow {
            font-size: .9rem; 
            color: #fb9a05; 
        }

        .block {
            font-size: .8125rem; 
            font-weight: 600; 
            color: #8898aa; 
            line-height: 1.8em; 
            margin: 0; 
        }

        .ip-address {
            color: #2dce89; 
            margin-bottom: 0.5rem; 
        }

        .info {
            color: #fb6340; 
        }
    </style>
</head>
<body>
<?php if (in_array($lang, ['zh-cn', 'en', 'auto'])): ?>
    <fieldset class="cbi-section">
        <div class="status">
            <div class="img-con">
                <img src="/nekoclash/assets/neko/img/loading.svg" id="flag" class="pure-img" title="National Fla">
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
                country = (navigator.language === 'en') ? 'China Taiwan' : 'China Taiwan';
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
<?php
$singbox_status = 0;
$neko_status = 0;

$logDir = '/etc/neko/tmp/';
$logFile = $logDir . 'log.txt';
$kernelLogFile = $logDir . 'neko_log.txt';
$singBoxLogFile = '/var/log/singbox_log.txt';
$singboxStartLogFile = $logDir . 'singbox_start_log.txt';

$singBoxPath = '/usr/bin/sing-box';
$configDir = '/etc/neko/config';

$start_script_template = <<<EOF
#!/bin/bash

if command -v fw4 > /dev/null; then
    echo "Detected fw4, configuring nftables rules..."

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
    echo "Detected fw3, configuring iptables rules..."

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

echo "Starting sing-box, using configuration file: %s"
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

function isMihomoRunning() {
    $command = "ps w | grep 'mihomo' | grep -v grep";
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

if (isMihomoRunning()) {
    $neko_status = 1;
} else {
    $neko_status = 0;
}

if ($singbox_status == 1) {
    $runningConfigFile = getRunningConfigFile();
    if ($runningConfigFile) {
        $str_cfg = htmlspecialchars(basename($runningConfigFile));
    } else {
        $str_cfg = 'Sing-box configuration file: No running configuration file found';
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
        exec("service firewall restart", $output, $returnVar);

        if ($returnVar === 0) {
            logToFile('/etc/neko/tmp/log.txt', "Firewall restarted successfully.");
            return true;
        } else {
            logToFile('/etc/neko/tmp/log.txt', "Failed to restart firewall.");
            error_log("Failed to stop Sing-box with PID $pid");
        }
    }
    return false;
}

function logToFile($filePath, $message) {
    $timestamp = date('Y-m-d H:i:s');
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
            $logMessage = $returnVar === 0 
               ? "Sing-box has been started, version: $version" : "Failed to start Sing-box";
            logToFile($logFile, $logMessage);
            $singbox_status = $returnVar === 0 ? 1 : 0;
        } elseif ($_POST['singbox'] === 'disable') {
            $success = stopSingbox();
            $logMessage = $success ? "Sing-box stopped" : "Failed to stop Sing-box";
            logToFile($logFile, $logMessage);
            $singbox_status = $success ? 0 : $singbox_status;
        } elseif ($_POST['singbox'] === 'restart') {
            $success = stopSingbox();
            if ($success) {
                checkLogFileSize($singBoxLogFile, $maxFileSize);
                applyFirewallRules();
                createStartScript($configFile);
                exec("/etc/neko/core/start.sh > $singBoxLogFile 2>&1 &", $output, $returnVar);
                $version = getSingboxVersion();
                $pid = getSingboxPID();
                $logMessage = $returnVar === 0 
                    ? "Sing-box restarted, version: $version, PID: $pid"
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
        $message = 'Sing-box runtime log cleared';
    }

    if (isset($_POST['clear_plugin_log'])) {
        file_put_contents($logFile, '');
        $message = 'Plugin log cleared';
    }

    if (isset($_POST['clear_kernel_log'])) {
        file_put_contents($kernelLogFile, '');
        $message = 'Kernel log cleared';
    }
}

function readLogFile($filePath) {
    if (file_exists($filePath)) {
        return nl2br(htmlspecialchars(readRecentLogLines($filePath, 1000)));
    } else {
        return 'Log file does not exist.';
    }
}

$logContent = readLogFile($logFile);
$kernelLogContent = readLogFile($kernelLogFile);
$singboxLogContent = readLogFile($singBoxLogFile);
$singboxStartLogContent = readLogFile($singboxStartLogFile);
?>
<div class="container container-bg border border-3 col-12 mb-4">
    <h2 class="text-center p-2">NekoClash Control Panel</h2>
    <table class="table table-borderless mb-2">
        <tbody>
            <tr>
            <style>
            .btn-group .btn {
            width: 100%; 
            }
            </style>
                <td>Status</td>
                <td class="d-grid">
                    <div class="btn-group col" role="group" aria-label="ctrl">
                        <?php
                            if ($neko_status == 1) {
                                echo "<button type=\"button\" class=\"btn btn-success\">Mihomo is running</button>\n";
                            } else {
                                echo "<button type=\"button\" class=\"btn btn-outline-danger\">Mihomo is not running</button>\n";
                            }

                            echo "<button type=\"button\" class=\"btn btn-warning d-grid\">$str_cfg</button>\n";

                            if ($singbox_status == 1) {
                                echo "<button type=\"button\" class=\"btn btn-success\">Sing-box is running</button>\n";
                            } else {
                                echo "<button type=\"button\" class=\"btn btn-outline-danger\">Sing-box is not running</button>\n";
                            }
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Control</td>
                <form action="index.php" method="post">
                    <td class="d-grid">
                        <div class="btn-group col" role="group" aria-label="ctrl">
                            <button type="submit" name="neko" value="start" class="btn btn<?php if ($neko_status == 1) echo "-outline" ?>-info <?php if ($neko_status == 1) echo "disabled" ?> d-grid">Enable Mihomo</button>
                            <button type="submit" name="neko" value="disable" class="btn btn<?php if ($neko_status == 0) echo "-outline" ?>-danger <?php if ($neko_status == 0) echo "disabled" ?> d-grid">Disable Mihomo</button>
                            <button type="submit" name="neko" value="restart" class="btn btn<?php if ($neko_status == 0) echo "-outline" ?>-warning <?php if ($neko_status == 0) echo "disabled" ?> d-grid">Restart Mihomo</button>
                        </div>
                    </td>
                </form>
                <form action="index.php" method="post">
                    <td class="d-grid">   
                        <select name="config_file" id="config_file" class="form-select">
                            <?php foreach ($availableConfigs as $config): ?>
                                <option value="<?= htmlspecialchars($config) ?>" <?= isset($_POST['config_file']) && $_POST['config_file'] === $config ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(basename($config)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="btn-group col" role="group" aria-label="ctrl">
                            <button type="submit" name="singbox" value="start" class="btn btn<?php if ($singbox_status == 1) echo "-outline" ?>-info <?php if ($singbox_status == 1) echo "disabled" ?> d-grid">Enable Sing-box</button>
                            <button type="submit" name="singbox" value="disable" class="btn btn<?php if ($singbox_status == 0) echo "-outline" ?>-danger <?php if ($singbox_status == 0) echo "disabled" ?> d-grid">Disable Sing-box</button>
                            <button type="submit" name="singbox" value="restart" class="btn btn<?php if ($singbox_status == 0) echo "-outline" ?>-warning <?php if ($singbox_status == 0) echo "disabled" ?> d-grid">Restart Sing-box</button>
                        </div>
                    </td>
                </form>
            </tr>
            <tr>
                <td>Running Mode</td>
                <td class="d-grid">
                     <?php
                     $mode_placeholder = '';
                     if ($neko_status == 1) {
                         $mode_placeholder = $neko_cfg['echanced'] . " | " . $neko_cfg['mode'];
                     } elseif ($singbox_status == 1) {
                         $mode_placeholder = "Rule Mode";
                     } else {
                         $mode_placeholder = "Not running";
                     }
                     ?>
            <input class="form-control text-center" name="mode" type="text" placeholder="<?php echo $mode_placeholder; ?>" disabled>
        </td>
    </tr>
</tbody>
    </table>
</div>

<div class="container container-bg border border-3 rounded-4 col-12 mb-4">
   <h2 class="text-center p-2">System Information</h2>
    <table class="table table-borderless mb-2">
        <tbody>
            <tr>
                <td>Devices</td>
                <td class="col-7"><?php echo $devices ?></td>
            </tr>
            <tr>
                <td>RAM</td>
                <td class="col-7"><?php echo "$ramUsage/$ramTotal MB" ?></td>
            </tr>
            <tr>
                <td>OS Version</td>
                <td class="col-7"><?php echo $OSVer ?></td>
            </tr>
            <tr>
                <td>Kernel Version</td>
                <td class="col-7"><?php echo $kernelv ?></td>
            </tr>
            <tr>
                <td>Average Load</td>
                <td class="col-7"><?php echo "$cpuLoadAvg1Min $cpuLoadAvg5Min $cpuLoadAvg15Min" ?></td>
            </tr>
            <tr>
                <td>Uptime</td>
              <td class="col-7"><?php echo "{$days} days {$hours} hours  {$minutes} minutes  {$seconds} seconds"; ?></td>
            </tr>
        </tbody>
    </table>
</div>
<div class="container container-bg border border-3 rounded-4 col-12 mb-4">
    <table class="table table-borderless mb-0">
        <tbody>
            <tr class="text-center">
               <td class="col-2">D-Total</td>
               <td class="col-2">U-Total</td>
            </tr>
            <tr class="text-center">
                <td class="col-2"><class id="downtotal">-</class></td>
                <td class="col-2"><class id="uptotal">-</class></td>
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
    .log-container {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-width: 0;
    }
    .log-header {
        text-align: center;
        margin-bottom: 10px;
        font-size: 1.1rem;
    }
    .log-footer {
        display: flex;
        justify-content: center;
        margin-top: auto;
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
    .log-section {
        margin-bottom: 20px;
        border: 2px solid #c0c0c0;
        padding: 10px;
        border-radius: 8px;
    }
    .nav-buttons {
        display: flex;
        flex-wrap: nowrap; 
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
        overflow-x: auto; 
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
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .nav-buttons {
            flex-wrap: wrap; 
        }
        .nav-buttons a {
            display: block;
            width: 100%;
        }
    }
</style>
</head>
<body>
    <div class="container container-bg border border-3 rounded-4 col-12 mb-4">
        <h2 class="text-center p-2">Logs</h2>
        <div class="log-section">
            <div class="log-container">
                <h4 class="log-header">Plugin Logs</h4>
                <pre class="form-control"><?php echo htmlspecialchars($logContent, ENT_QUOTES, 'UTF-8'); ?></pre>
                <form action="index.php" method="post" class="mt-3 log-footer">
                    <button type="submit" name="clear_plugin_log" class="btn btn-danger btn-clear-log">Clear Plugin Logs</button>
                </form>
            </div>
        </div>
        <div class="log-section">
            <div class="log-container">
                <h4 class="log-header">Mihomo Logs</h4>
                <pre class="form-control"><?php echo htmlspecialchars($kernelLogContent, ENT_QUOTES, 'UTF-8'); ?></pre>
                <form action="index.php" method="post" class="mt-3 log-footer">
                    <button type="submit" name="clear_kernel_log" class="btn btn-danger btn-clear-log">Clear Mihomo Logs</button>
                </form>
            </div>
        </div>
        <div class="log-section">
            <div class="log-container">
                <h4 class="log-header">Sing-box Logs</h4>
                <pre class="form-control"><?php echo htmlspecialchars($singboxLogContent, ENT_QUOTES, 'UTF-8'); ?></pre>
                <form action="index.php" method="post" class="mt-3 log-footer">
                    <button type="submit" name="clear_singbox_log" class="btn btn-danger btn-clear-log">Clear Sing-box Logs</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container container-bg border border-3 rounded-4 col-12 mb-4 d-flex align-items-center justify-content-center" style="height: 100%;">
        <div class="nav-buttons text-center" style="height: 100%;">
            <a href="/nekoclash/upload.php" class="config-menu-button d-block mb-2" onclick="speakAndNavigate('Open Mihomo Management Panel', '/nekoclash/upload.php'); return false;">Open Mihomo Management Panel</a>
            <a href="/nekoclash/upload_sb.php" class="monitoring-button d-block mb-2" onclick="speakAndNavigate('Open Sing-box Management Panel', '/nekoclash/upload_sb.php'); return false;">Open Sing-box Management Panel</a>
            <a href="/nekoclash/box.php" class="box-menu-button d-block mb-2" onclick="speakAndNavigate('Open Sing-box Conversion Template', '/nekoclash/box.php'); return false;">Open Sing-box Conversion Template</a>
            <a href="/nekoclash/personal.php" class="current-menu-button d-block mb-2" onclick="speakAndNavigate('Open Mihomo Personal Edition', '/nekoclash/personal.php'); return false;">Open Mihomo Personal Edition</a>
            <a href="/nekoclash/mon.php" class="main-menu-button d-block mb-2" onclick="speakAndNavigate('Open Sing-box Monitoring Panel', '/nekoclash/mon.php'); return false;">Open Sing-box Monitoring Panel</a>
        </div>
    </div>

    <script src="/www/nekoclash/assets/js/bootstrap.bundle.min.js"></script>
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
