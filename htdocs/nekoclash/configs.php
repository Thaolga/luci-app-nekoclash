<?php
include './cfg.php';

$dirPath = "$neko_dir/config";
$tmpPath = "$neko_www/lib/selected_config.txt";
$arrFiles = array();
$arrFiles = array_merge(glob("$dirPath/*.yaml"), glob("$dirPath/*.json")); 


$error = "";

if (isset($_POST['clashconfig'])) {
    $dt = $_POST['clashconfig'];
    
    $fileContent = file_get_contents($dt);

    json_decode($fileContent);
    if (json_last_error() === JSON_ERROR_NONE || pathinfo($dt, PATHINFO_EXTENSION) === 'yaml') {
        shell_exec("echo $dt > $tmpPath");
        $selected_config = $dt;
    } else {
        $error = "The selected file content is not a valid JSON format. Please choose another configuration file.。"; 
    }
}
if(isset($_POST['neko'])){
    $dt = $_POST['neko'];
    if ($dt == 'apply') shell_exec("$neko_dir/core/neko -r");
}
include './cfg.php';
?>
<!doctype html>
<html lang="en" data-bs-theme="<?php echo substr($neko_theme,0,-4) ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configs - Neko</title>
    <link rel="icon" href="./assets/img/favicon.png">
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/css/custom.css" rel="stylesheet">
    <link href="./assets/theme/<?php echo $neko_theme ?>" rel="stylesheet">
    <script type="text/javascript" src="./assets/js/feather.min.js"></script>
    <script type="text/javascript" src="./assets/js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="./assets/js/bootstrap.min.js"></script>
  </head>
  <body>
    <style>
        .container-sm {
            margin: 20px auto;
        }
    </style>
</head>
<body>
    <div class="container-sm text-center col-8">
        <img src="./assets/img/photo.png" class="img-fluid mb-5 draggable" style="display: none;">
    </div>

    <script>
        function toggleImage() {
            var img = document.querySelector('.container-sm img');
            var btn = document.getElementById('showHideButton');
            if (img.style.display === 'none') {
                img.style.display = 'block';
                btn.innerText = 'Hide Icon';
            } else {
                img.style.display = 'none';
                btn.innerText = 'Show Icon';
            }
        }

        function hideIcon() {
            var img = document.querySelector('.container-sm img');
            var btn = document.getElementById('showHideButton');
            if (img.style.display === 'block') {
                img.style.display = 'none';
                btn.innerText = 'Show Icon';
            }
        }

        document.body.ondblclick = function() {
            toggleImage();
        };
    </script>
    <div class="container-sm container-bg text-center callout border border-3 rounded-4 col-11">
        <div class="row">
            <a href="./" class="col btn btn-lg">Home</a>
            <a href="./dashboard.php" class="col btn btn-lg">Dashboard</a>
            <a href="#" class="col btn btn-lg">Configs</a>
            <a href="./settings.php" class="col btn btn-lg">Settings</a>
        </div>
    </div>
    <div class="container text-left p-3">
        
        <div class="container container-bg border border-3 rounded-4 col-12 mb-4">
            <h2 class="text-center p-2">Configs</h2>
            <form action="configs.php" method="post">
                <div class="container text-center justify-content-md-center">
                    <div class="row justify-content-md-center">
                        <div class="col input-group mb-3 justify-content-md-center">
                          <select class="form-select" name="clashconfig" aria-label="themex">
                            <option selected><?php echo $selected_config ?></option>
                            <?php foreach ($arrFiles as $file) echo "<option value=\"".$file.'">'.$file."</option>" ?>
                          </select>
                        </div>
                        <div class="row justify-content-md-center">
                            <div class="btn-group d-grid d-md-flex justify-content-md-center mb-5" role="group">
                              <input class="btn btn-info" type="submit" value="Change Configs">
                              <button name="neko" type="submit" value="apply" class="btn btn-warning d-grid">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
<div class="container container-bg border border-3 rounded-4 col-12 mb-4"></br>
    <ul class="nav text-center justify-content-md-center">
        <li class="nav-item">
                    <a class="col btn btn-lg active" data-bs-toggle="tab" href="#info">Info</a>
                </li>
                <li class="nav-item">
                    <a class="col btn btn-lg" data-bs-toggle="tab" href="#proxy">Proxy</a>
                </li>
                <li class="nav-item">
                    <a class="col btn btn-lg" data-bs-toggle="tab" href="#rules">Rules</a>
                </li>
                <li class="nav-item">
                    <a class="col btn btn-lg" data-bs-toggle="tab" href="#converter">Converter</a>
                </li>
        <li class="nav-item">
            <a class="col btn btn-lg" data-bs-toggle="tab" href="#upload">Subscribe</a>
        </li>
    </ul>
      </div>
           <div class="tab-content">
                <div id="info" class="tab-pane fade show active">
                    <h2 class="text-center p-2">Config Information</h2>
                    <table class="table table-borderless callout mb-5">
                        <tbody>
                            <tr class="text-center">
                                <td class="col-2">PORT</td>
                                <td class="col-2">REDIR</td>
                                <td class="col-2">SOCKS</td>
                            </tr>
                            <tr class="text-center">
                                <td class="col-2">
                                    <input class="form-control text-center" name="port" type="text" placeholder="<?php echo $neko_cfg['port'] ?>" disabled>
                                </td>
                                <td class="col-2">
                                    <input class="form-control text-center" name="redir" type="text" placeholder="<?php echo $neko_cfg['redir'] ?>" disabled>
                                </td>
                                <td class="col-2">
                                    <input class="form-control text-center" name="socks" type="text" placeholder="<?php echo $neko_cfg['socks'] ?>" disabled>
                                </td>
                            </tr>
                            <tr class="text-center">
                                <td class="col-2">MIXED</td>
                                <td class="col-2">TPROXY</td>
                                <td class="col-2">MODE</td>
                            </tr>
                            <tr class="text-center">
                                <td class="col-2">
                                    <input class="form-control text-center" name="mixed" type="text" placeholder="<?php echo $neko_cfg['mixed'] ?>" disabled>
                                </td>
                                <td class="col-2">
                                    <input class="form-control text-center" name="tproxy" type="text" placeholder="<?php echo $neko_cfg['tproxy'] ?>" disabled>
                                </td>
                                <td class="col-2">
                                    <input class="form-control text-center" name="mode" type="text" placeholder="<?php echo $neko_cfg['mode'] ?>" disabled>
                                </td>
                            </tr>
                            <tr class="text-center">
                                <td class="col-2">ENHANCED</td>
                                <td class="col-2">SECRET</td>
                                <td class="col-2">CONTROLLER</td>
                            </tr>
                            <tr class="text-center">
                                <td class="col-2">
                                    <input class="form-control text-center" name="ech" type="text" placeholder="<?php echo $neko_cfg['echanced'] ?>" disabled>
                                </td>
                                <td class="col-2">
                                    <input class="form-control text-center" name="sec" type="text" placeholder="<?php echo $neko_cfg['secret'] ?>" disabled>
                                </td>
                                <td class="col-2">
                                    <input class="form-control text-center" name="ext" type="text" placeholder="<?php echo $neko_cfg['ext_controller'] ?>" disabled>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h2 class="text-center p-2">Configs</h2>
                    <div class="container h-100 mb-5">
                        <iframe class="rounded-4 w-100" scrolling="no" height="700" src="./configconf.php" title="yacd" allowfullscreen></iframe>
                    </div>
                </div>
                <div id="proxy" class="tab-pane fade">
                    <h2 class="text-center p-2">Proxy Editor</h2>
                    <div class="container h-100 mb-5">
                        <iframe class="rounded-4 w-100" scrolling="no" height="700" src="./proxyconf.php" title="yacd" allowfullscreen></iframe>
                    </div>
                </div>
                <div id="rules" class="tab-pane fade">
                    <h2 class="text-center p-2">Rules Editor</h2>
                    <div class="container h-100 mb-5">
                        <iframe class="rounded-4 w-100" scrolling="no" height="700" src="./rulesconf.php" title="yacd" allowfullscreen></iframe>
                    </div>
                </div>
                <div id="converter" class="tab-pane fade">
                    <h2 class="text-center p-2 mb-5">YAML Converter</h2>
                    <div class="container h-100">
                        <iframe class="rounded-4 w-100" scrolling="no" height="700" src="./yamlconv.php" title="yacd" allowfullscreen></iframe>
                         </div>
                </div>
                    <div id="upload" class="tab-pane fade">
    <?php
    $subscriptionPath = '/etc/neko/proxy_provider/';
    $subscriptionFile = $subscriptionPath . 'subscriptions.json';
    $autoUpdateConfigFile = $subscriptionPath . 'auto_update_config.json';

    $message = "";
    $subscriptions = [];
    $autoUpdateConfig = ['auto_update_enabled' => false, 'update_time' => '00:00'];

    if (!file_exists($subscriptionPath)) {
        mkdir($subscriptionPath, 0755, true);
    }

    if (!file_exists($subscriptionFile)) {
        file_put_contents($subscriptionFile, json_encode([]));
    }

    if (!file_exists($autoUpdateConfigFile)) {
        file_put_contents($autoUpdateConfigFile, json_encode($autoUpdateConfig));
    }

    $subscriptions = json_decode(file_get_contents($subscriptionFile), true);
    if (!$subscriptions) {
        for ($i = 0; $i < 7; $i++) {
            $subscriptions[$i] = [
                'url' => '',
                'file_name' => "subscription_{$i}.yaml",
            ];
        }
    }

    $autoUpdateConfig = json_decode(file_get_contents($autoUpdateConfigFile), true);

    if (isset($_POST['update'])) {
        $index = intval($_POST['index']);
        $url = $_POST['subscription_url'] ?? '';
        $customFileName = $_POST['custom_file_name'] ?? "subscription_{$index}.yaml";

        $subscriptions[$index]['url'] = $url;
        $subscriptions[$index]['file_name'] = $customFileName;

        if (!empty($url)) {
            $finalPath = $subscriptionPath . $customFileName;
            $command = "curl -fsSL -o {$finalPath} {$url}";
            exec($command . ' 2>&1', $output, $return_var);

            if ($return_var === 0) {
                $message = "Subscription link {$url} updated successfully! File has been saved to: {$finalPath}";
            } else {
                $message = "Configuration update failed! Error message:" . implode("\n", $output);
            }
        } else {
            $message = "Subscription link number " . ($index + 1) . " is empty!";
        }

        file_put_contents($subscriptionFile, json_encode($subscriptions));
    }

    if (isset($_POST['set_auto_update'])) {
        $updateTime = $_POST['update_time'] ?? '00:00';
        $autoUpdateEnabled = isset($_POST['auto_update_enabled']);

        $autoUpdateConfig = [
            'auto_update_enabled' => $autoUpdateEnabled,
            'update_time' => $updateTime
        ];

        file_put_contents($autoUpdateConfigFile, json_encode($autoUpdateConfig));
        $message = "Automatic update settings have been saved!";
    }
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mihomo Subscription Program</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }
    .container {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }
    .text-center {
        text-align: center;
    }
    .input-group {
        margin-bottom: 15px;
    }
    .input-group label {
        display: block;
        margin-bottom: 5px;
    }
    .input-group input,
    .input-group textarea,
    .input-group select {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
    }
    .btn {
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        color: white;
        border-radius: 4px;
        text-align: center;
        display: inline-block;
        text-decoration: none;
    }
    .btn-primary {
        background-color: #007bff; 
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }
    footer {
        color: white;
        padding: 10px;
        text-align: center;
    }
    .container-bg {
    }
    .border {
        border: 1px solid #ccc;
    }
    .rounded-4 {
        border-radius: 4px;
    }
    .form-spacing {
        margin: 20px 0;
    }
    .input-group input,
    .input-group textarea,
    .input-group select {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
    background-color: #B0C4DE; 
    border: 1px solid #ccc; 
    }
</style>

    </head>
    <body>
        <div class="container">
            <h1 class="text-center" style="color: #00FF7F;">Mihomo Subscription Program</h1>
            <p class="help-text text-center">
                Mihomo subscription supports all formats: Base64, Clash format, and node links.
                <br><br>
                <a href="/nekoclash/upload.php" class="btn btn-primary">Open Mihomo Subscription Manager</a>
                <a href="/nekoclash/upload_sb.php" class="btn btn-primary">Sing-box Dedicated Subscription Manager</a>
                <br><br>     
            </p>
<h2 class="text-center" style="color: #00FF7F;">Subscription Management</h2>
 <div class="form-spacing"></div>
            <?php if ($message): ?>
                <p><?php echo nl2br(htmlspecialchars($message)); ?></p>
            <?php endif; ?>
            <?php for ($i = 0; $i < 7; $i++): ?>
                <form method="post" class="mb-3">
                    <div class="input-group">
                        <label for="subscription_url_<?php echo $i; ?>" class="sr-only">Subscription Link<?php echo ($i + 1); ?>:</label>
                        <input type="text" name="subscription_url" id="subscription_url_<?php echo $i; ?>" value="<?php echo htmlspecialchars($subscriptions[$i]['url']); ?>" required class="form-control">
                        <input type="text" name="custom_file_name" id="custom_file_name_<?php echo $i; ?>" value="<?php echo htmlspecialchars($subscriptions[$i]['file_name']); ?>" class="form-control ml-2" placeholder="Custom Filename">
                        <input type="hidden" name="index" value="<?php echo $i; ?>">
                        <button type="submit" name="update" class="btn btn-primary btn-custom ml-2">Update Configuration</button>
               </div>
     </form>
<?php endfor; ?>
<div class="container container-bg border border-3 rounded-4 col-12 mb-4">
    <h2 class="text-center p-2 mb-3">Tip</h2>
    <div class="container text-center border border-3 rounded-4 col-10 mb-4">
    <p style="color: #87CEEB; text-align: left;">
    <p style="text-align: center; font-size: 24px; color: #87CEEB;">
    <strong>Player Function Description</strong>
    </p>

    <p style="color: #87CEEB; text-align: left;">
    <strong>1.Song Push and Control:</strong><br>
    &emsp; 1 The player pushes songs through the GitHub playlist.<br>
    &emsp; 2 Use the keyboard arrow keys to switch songs.<br>
    &emsp; 3 Enter <code>nekoclash</code> in the terminal to update the client and core.。<br>
    &emsp; 4 Sing-box features a built-in intelligent conversion mechanism that automatically adapts to subscriptions from any provider, with no additional configuration needed.<br><br>

    <strong>2. Playback Function:</strong><br>
    &emsp; 1 Auto-play Next Song: If enabled, the player will automatically play the next song. When the song list reaches the end, it will loop back to the first song.<br>
    &emsp; 2 Enable/Disable Playback: Click or press the Escape key to enable or disable playback. When disabled, the current playback will stop, and no new songs can be selected or played.<br><br>

    <strong>3. Keyboard Control：</strong><br>
    &emsp; 1 Provides quick control with the arrow keys ⇦ ⇨ and the space bar, supporting switching between songs and play/pause functions.<br><br>

    <strong>4. Playback Modes:</strong><br>
    &emsp; 1 Loop Playback and Sequential Playback: You can switch between loop and sequential playback modes using the buttons and the ⇧ key on the keyboard.
    </p>
        <?php
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            $output = [];
            $return_var = 0;
            exec('uci get network.lan.ipaddr 2>&1', $output, $return_var);
            $routerIp = trim(implode("\n", $output));

            function isValidIp($ip) {
                $parts = explode('.', $ip);
                if (count($parts) !== 4) return false;
                foreach ($parts as $part) {
                    if (!is_numeric($part) || (int)$part < 0 or (int)$part > 255) return false;
                }
                return true;
            }

            if (isValidIp($routerIp) && !in_array($routerIp, ['0.0.0.0', '255.255.255.255'])) {
                $controlPanelUrl = "http://$routerIp/nekoclash";
                echo '<span style="color: #87CEEB;">Standalone Control Panel Address:</span> <a href="' . $controlPanelUrl . '" style="color: red;" target="_blank"><code>' . $controlPanelUrl . '</code></a><br>';
            } else {
                echo "Unable to retrieve the router's IP address. Error message: $routerIp";
            }
            ?>
        </p>
    </div>
</div>

<footer class="text-center">
    <p><?php echo $footer ?></p>
</footer>
</div>
</body>
</html>
