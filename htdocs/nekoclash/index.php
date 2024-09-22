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
            <a href="#" class="col btn btn-lg">Home</a>
            <a href="./dashboard.php" class="col btn btn-lg">Dashboard</a>
            <a href="./configs.php" class="col btn btn-lg">Configs</a>
            <a href="./settings.php" class="col btn btn-lg">Settings</a>
        </div>
    </div>
    <div class="container text-left p-3">
       
        <div class="container container-bg border border-3 rounded-4 col-12 mb-4">
    <h2 class="text-center p-2" style="margin-top: -15px; margin-bottom: 5px;">Running Status</h2>
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
            height: 80px;
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
        $message = 'Sing-box runtime log cleared';
    }

    if (isset($_POST['clear_plugin_log'])) {
        file_put_contents($logFile, ''); 
        $message = 'Plugin log cleared';
    }
}

function readLogFile($filePath) {
    if (file_exists($filePath)) {
        return nl2br(htmlspecialchars(readRecentLogLines($filePath, 1000), ENT_NOQUOTES));
    } else {
        return 'Log file does not exist.';
    }
}

$logContent = readLogFile($logFile); 
$singboxLogContent = readLogFile($singBoxLogFile); 
$singboxStartLogContent = readLogFile($singboxStartLogFile); 
?>

<div class="container container-bg border border-3 col-12 mb-4 p-1">
    <h2 class="text-center p-1">NekoClash Control Panel</h2>
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

                            echo "<button type=\"button\" class=\"btn btn-deepskyblue\">$str_cfg</button>\n";

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
                            <button type="submit" name="neko" value="start" class="btn btn<?php if ($neko_status == 1) echo "-outline" ?>-success <?php if ($neko_status == 1) echo "disabled" ?> d-grid">Enable Mihomo</button>
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
   <h2 class="text-center p-2" style="margin-top: -15px; margin-bottom: 5px;">System Information</h2>
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
<div class="container container-bg border border-3 rounded-4 col-12 mb-4">
        <h2 class="text-center p-2">Logs</h2>
        <div class="d-flex flex-wrap">
            <div class="log-section">
                <div class="log-container">
                    <h4 class="log-header">Plugin Logs</h4>
                    <pre id="plugin_log" class="form-control" style="overflow-y: scroll;"></pre>
                    <form action="index.php" method="post" class="mt-3 log-footer">
                        <button type="submit" name="clear_plugin_log" class="btn btn-danger btn-clear-log">Clear Log</button>
                    </form>
                </div>
            </div>

            <div class="log-section">
                <div class="log-container">
                    <h4 class="log-header">Mihomo Logs</h4>
                    <pre id="bin_logs" class="form-control" style="overflow-y: scroll;"></pre> 
                    <form action="index.php" method="post" class="mt-3 log-footer">
                        <button type="submit" name="neko" value="clear" class="btn btn-danger btn-clear-log">Clear Log</button>
                    </form>
                </div>
            </div>

            <div class="log-section">
                <div class="log-container">
                    <h4 class="log-header">Sing-box Logs</h4>
                    <pre id="singbox_log" class="form-control" style="overflow-y: scroll;"></pre>
                    <form action="index.php" method="post" class="mt-3 log-footer">
                        <button type="submit" name="clear_singbox_log" class="btn btn-danger btn-clear-log">Clear Log</button>
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

<a href="/nekoclash/mon.php" id="move-button" class="config-menu-button d-flex justify-content-center align-items-center" 
   style="height: 50px; width: 50px; line-height: 50px; border-radius: 50%; background-color: #28a745; color: white; position: absolute; top: 20px; left: 20px; text-align: center; text-decoration: none; transition: background-color 0.3s, top 0.5s, left 0.5s;" 
   onclick="speakAndNavigate('Open Mihomo Management Panel', '/nekoclash/mon.php'); return false;"
   onmouseover="this.style.backgroundColor='#218838';" onmouseout="this.style.backgroundColor='#28a745';">
    Panel
</a>

<script>
const button = document.getElementById('move-button');
const originalPosition = { top: '20px', left: '20px' };
let isDragging = false;
let offsetX, offsetY;
let returnTimeout;

button.addEventListener('mousedown', (e) => {
    isDragging = true;
    offsetX = e.clientX - button.getBoundingClientRect().left;
    offsetY = e.clientY - button.getBoundingClientRect().top;

    clearTimeout(returnTimeout); 
});

document.addEventListener('mousemove', (e) => {
    if (isDragging) {
        button.style.left = `${e.clientX - offsetX}px`;
        button.style.top = `${e.clientY - offsetY}px`;

        clearTimeout(returnTimeout);
        returnTimeout = setTimeout(() => {
            returnToOriginalPosition();
        }, 30000); 
    }
});

document.addEventListener('mouseup', () => {
    isDragging = false;
});

function returnToOriginalPosition() {
    button.style.top = originalPosition.top;
    button.style.left = originalPosition.left;
}

button.style.top = originalPosition.top;
button.style.left = originalPosition.left;
</script>
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

