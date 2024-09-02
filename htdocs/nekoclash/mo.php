<?php

ob_start();
include './cfg.php';
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
    $subscriptions = [];
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
            $message = "Subscription link {$url} updated successfully! File saved to: {$finalPath}";
        } else {
            $message = "Configuration update failed! Error message: " . implode("\n", $output);
        }
    } else {
        $message = "The subscription link #" . ($index + 1) . " is empty!";
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
    $message = "Auto-update settings have been saved!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mihomo Subscription Program</title>

    <link href="./assets/theme/NavajoWhite.css" rel="stylesheet">

    <style>
        body.container-bg {
            background-color: #FFDEAD;
            color: #000;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            padding: 20px;
            margin-top: 0;
        }
        .btn-primary {
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-large {
            padding: 15px 30px; /* Increase padding */
            font-size: 18px; /* Increase font size */
        }
        .btn-rounded {
            border-radius: 12px; /* Rounded corners */
        }
        .form-control {
            background-color: #fff;
            color: #000;
            border-color: #ccc;
            margin-bottom: 10px;
        }
        .form-spacing {
            margin-bottom: 10px;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body class="container-bg">
    <div class="container">
        <h1 class="text-center" style="color: #00FF7F;">Mihomo Subscription Program</h1>
        <p class="help-text text-center">
            Mihomo subscriptions support all formats: Base64 / Clash format / Node links        
        </p>
        <h2 class="text-center" style="color: #00FF7F;">Subscription Management</h2>
        <div class="form-spacing"></div>
        <?php if ($message): ?>
            <p><?php echo nl2br(htmlspecialchars($message)); ?></p>
        <?php endif; ?>
        <?php for ($i = 0; $i < 7; $i++): ?>
            <form method="post" class="mb-3">
                <div class="input-group">
                    <label for="subscription_url_<?php echo $i; ?>">Subscription Link <?php echo ($i + 1); ?>:</label>
                    <input type="text" name="subscription_url" id="subscription_url_<?php echo $i; ?>" value="<?php echo htmlspecialchars($subscriptions[$i]['url']); ?>" required class="form-control">
                    <label for="custom_file_name_<?php echo $i; ?>">Custom File Name:</label>
                    <input type="text" name="custom_file_name" id="custom_file_name_<?php echo $i; ?>" value="<?php echo htmlspecialchars($subscriptions[$i]['file_name']); ?>" class="form-control">
                    <input type="hidden" name="index" value="<?php echo $i; ?>">
                    <button type="submit" name="update" class="btn btn-primary">Update Configuration</button>
                </div>
            </form>
        <?php endfor; ?>
    </div>
</body>
</html>
