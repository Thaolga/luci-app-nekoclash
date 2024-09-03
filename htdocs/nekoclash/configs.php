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
            <a class="col btn btn-lg active" data-bs-toggle="tab" href="#info">Configuration</a>
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
            <a class="col btn btn-lg" data-bs-toggle="tab" href="#upload">Subscription</a>
        </li>
        <li class="nav-item">
            <a class="col btn btn-lg" data-bs-toggle="tab" href="#tip">Tips</a>
        </li>
    </ul>
</div>

<div class="container container-bg border border-3 rounded-4 col-12 mb-4">
    <div class="tab-content">
        <div id="info" class="tab-pane fade show active">
            <h2 class="text-center p-2">Configuration Information</h2>
            <table class="table table-borderless callout mb-5">
                <!-- Table content remains unchanged -->
            </table>
            <h2 class="text-center p-2">Configuration</h2>
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
            <h2 class="text-center p-2 mb-5">Converter</h2>
            <div class="container h-100">
                <iframe class="rounded-4 w-100" scrolling="no" height="700" src="./yamlconv.php" title="yacd" allowfullscreen></iframe>
            </div>
        </div>

        <div id="upload" class="tab-pane fade">
            <h2 class="text-center p-2 mb-5">Subscription</h2>
            <div class="container h-100">
                <iframe class="rounded-4 w-100" scrolling="no" height="700" src="./mo.php" title="yacd" allowfullscreen></iframe>
            </div>
        </div>

        <div id="tip" class="tab-pane fade">
            <h2 class="text-center p-2 mb-3">Tips</h2>
            <div class="container text-center border border-3 rounded-4 col-10 mb-4">
                <p style="color: #87CEEB; text-align: left;">
                    <h1 style="font-size: 24px; color: #87CEEB; margin-bottom: 20px;"><strong>Player Function Description</strong></h1>
                    <div style="text-align: left; display: inline-block; margin-bottom: 20px;">
                        <strong>1. Song Push and Control:</strong><br>
                        &emsp; 1 The player pushes songs via GitHub playlists.<br>
                        &emsp; 2 Use the keyboard arrow keys to switch songs.<br>
                        &emsp; 3 Enter <code>nekoclash</code> in the terminal to update the client and core.<br>
                        &emsp; 4 Sing-box has a built-in intelligent conversion mechanism that automatically adapts to any subscription obtained from any provider, without additional configuration.<br><br>

                        <strong>2. Playback Function:</strong><br>
                        &emsp; 1 Auto-play the next song: If playback is enabled, the next song will automatically play. When the song list reaches the end, it will loop back to the first song.<br>
                        &emsp; 2 Enable/Disable Playback: Click or press the Escape key to enable or disable playback. When disabled, the current playback will stop, and no new songs can be selected or played.<br><br>

                        <strong>3. Keyboard Control:</strong><br>
                        &emsp; 1 Provides quick control with the arrow keys ⇦ ⇨ and spacebar, supporting switching between previous and next songs, and play/pause.<br><br>

                        <strong>4. Playback Modes:</strong><br>
                        &emsp; 1 Loop and Sequential Playback: You can switch between loop and sequential playback modes using buttons and the keyboard shortcut ⇧.<br><br>

                    </div>
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
                        echo '<div style="text-align: center; margin-top: 20px;"><span style="color: #87CEEB;">Standalone Control Panel Address:</span> <a href="' . $controlPanelUrl . '" style="color: red;" target="_blank"><code>' . $controlPanelUrl . '</code></a></div>';
                    } else {
                        echo "<div style='text-align: center; margin-top: 20px;'>Unable to retrieve the router IP address. Error message: $routerIp</div>";
                    }
                ?>
            </div>
        </div>
    </div>

    <footer style="text-align: center; margin-top: 20px;">
        <p><?php echo $footer ?></p>
    </footer>
</body>
</html>
