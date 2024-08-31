<?php
$uploadDir = '/etc/neko/proxy_provider/';
$configDir = '/etc/neko/config/';

ini_set('memory_limit', '256M');

date_default_timezone_set('Asia/Shanghai');

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!is_dir($configDir)) {
    mkdir($configDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['fileInput'])) {
        $file = $_FILES['fileInput'];
        $uploadFilePath = $uploadDir . basename($file['name']);

        if ($file['error'] === UPLOAD_ERR_OK) {
            if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
                echo 'File upload successful: ' . htmlspecialchars(basename($file['name']));
            } else {
                echo 'File upload failed!';
            }
        } else {
            echo 'Upload error: ' . $file['error'];
        }
    }

    if (isset($_FILES['configFileInput'])) {
        $file = $_FILES['configFileInput'];
        $uploadFilePath = $configDir . basename($file['name']);

        if ($file['error'] === UPLOAD_ERR_OK) {
            if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
                echo 'Configuration file upload successful: ' . htmlspecialchars(basename($file['name']));
            } else {
                echo 'Configuration file upload failed!';
            }
        } else {
            echo 'Upload error: ' . $file['error'];
        }
    }

    if (isset($_POST['deleteFile'])) {
        $fileToDelete = $uploadDir . basename($_POST['deleteFile']);
        if (file_exists($fileToDelete) && unlink($fileToDelete)) {
            echo 'File deletion successful: ' . htmlspecialchars(basename($_POST['deleteFile']));
        } else {
            echo 'File deletion failed!';
        }
    }

    if (isset($_POST['deleteConfigFile'])) {
        $fileToDelete = $configDir . basename($_POST['deleteConfigFile']);
        if (file_exists($fileToDelete) && unlink($fileToDelete)) {
            echo 'Configuration file deletion successful: ' . htmlspecialchars(basename($_POST['deleteConfigFile']));
        } else {
            echo 'Configuration file deletion failed!';
        }
    }

    if (isset($_POST['oldFileName'], $_POST['newFileName'], $_POST['fileType'])) {
        $oldFileName = basename($_POST['oldFileName']);
        $newFileName = basename($_POST['newFileName']);

        if ($_POST['fileType'] === 'proxy') {
            $oldFilePath = $uploadDir . $oldFileName;
            $newFilePath = $uploadDir . $newFileName;
        } elseif ($_POST['fileType'] === 'config') {
            $oldFilePath = $configDir . $oldFileName;
            $newFilePath = $configDir . $newFileName;
        } else {
            echo 'Invalid file type';
            exit;
        }

        if (file_exists($oldFilePath) && !file_exists($newFilePath)) {
            if (rename($oldFilePath, $newFilePath)) {
                echo 'File rename successful: ' . htmlspecialchars($oldFileName) . ' -> ' . htmlspecialchars($newFileName);
            } else {
                echo 'File rename failed!';
            }
        } else {
            echo 'File rename failed, file does not exist or new file name already exists.';
        }
    }

    if (isset($_POST['editFile']) && isset($_POST['fileType'])) {
        $fileToEdit = ($_POST['fileType'] === 'proxy') ? $uploadDir . basename($_POST['editFile']) : $configDir . basename($_POST['editFile']);
        $fileContent = '';
        $editingFileName = htmlspecialchars($_POST['editFile']);

        if (file_exists($fileToEdit)) {
            $handle = fopen($fileToEdit, 'r');
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $fileContent .= htmlspecialchars($line);
                }
                fclose($handle);
            } else {
                echo 'Unable to open file';
            }
        }
    }

    if (isset($_POST['saveContent'], $_POST['fileName'], $_POST['fileType'])) {
        $fileToSave = ($_POST['fileType'] === 'proxy') ? $uploadDir . basename($_POST['fileName']) : $configDir . basename($_POST['fileName']);
        $contentToSave = $_POST['saveContent'];
        file_put_contents($fileToSave, $contentToSave);
        echo '<p>File content updated: ' . htmlspecialchars(basename($fileToSave)) . '</p>';
    }

    if (isset($_FILES['customFileInput']) && isset($_POST['customDir'])) {
        $customDir = rtrim($_POST['customDir'], '/') . '/';
        if (!is_dir($customDir)) {
            if (!mkdir($customDir, 0755, true)) {
                echo 'Custom directory creation failed!';
            }
        }

        $file = $_FILES['customFileInput'];
        $uploadFilePath = $customDir . basename($file['name']);

        if ($file['error'] === UPLOAD_ERR_OK) {
            if (move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
                echo 'File uploaded to custom directory successfully: ' . htmlspecialchars(basename($file['name']));
            } else {
                echo 'File upload to custom directory failed!';
            }
        } else {
            echo 'Upload error: ' . $file['error'];
        }
    }

    if (isset($_GET['customFile'])) {
        $customDir = rtrim($_GET['customDir'], '/') . '/';
        $customFilePath = $customDir . basename($_GET['customFile']);
        if (file_exists($customFilePath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($customFilePath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($customFilePath));
            readfile($customFilePath);
            exit;
        } else {
            echo 'File does not exist!';
        }
    }
}

function formatFileModificationTime($filePath) {
    if (file_exists($filePath)) {
        $fileModTime = filemtime($filePath);
        return date('Y-m-d H:i:s', $fileModTime);
    } else {
        return 'File does not exist';
    }
}

$proxyFiles = scandir($uploadDir);
$configFiles = scandir($configDir);

if ($proxyFiles !== false) {
    $proxyFiles = array_diff($proxyFiles, array('.', '..'));
} else {
    $proxyFiles = []; 
}

if ($configFiles !== false) {
    $configFiles = array_diff($configFiles, array('.', '..'));
} else {
    $configFiles = []; 
}

function formatSize($size) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $unit = 0;
    while ($size >= 1024 && $unit < count($units) - 1) {
        $size /= 1024;
        $unit++;
    }
    return round($size, 2) . ' ' . $units[$unit];
}
?>
<?php
$subscriptionPath = '/etc/neko/proxy_provider/';
$subscriptionFile = $subscriptionPath . 'subscriptions.json';
$clashFile = $subscriptionPath . 'clash_config.yaml';

$message = "";
$decodedContent = ""; 
$subscriptions = [];

if (!file_exists($subscriptionPath)) {
    mkdir($subscriptionPath, 0755, true);
}

if (!file_exists($subscriptionFile)) {
    file_put_contents($subscriptionFile, json_encode([]));
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
        $message = "The {$index} subscription link is empty!";
    }

    file_put_contents($subscriptionFile, json_encode($subscriptions));
}

if (isset($_POST['convert_base64'])) {
    $base64Content = $_POST['base64_content'] ?? '';

    if (!empty($base64Content)) {
        $decodedContent = base64_decode($base64Content); 

        if ($decodedContent === false) {
            $message = "Base64 decoding failed, please check the input!";
        } else {
            $clashConfig = "# Clash Meta Config\n\n";
            $clashConfig .= $decodedContent;
            file_put_contents($clashFile, $clashConfig);
            $message = "Clash configuration file has been generated and saved to: {$clashFile}";
        }
    } else {
        $message = "Base64 content is empty!";
    }
}
?>
<?php

function parseVmess($base) {
    $decoded = base64_decode($base['host']);
    $arrjs = json_decode($decoded, true);

    if (json_last_error() !== JSON_ERROR_NONE || empty($arrjs['v'])) {
        return "DECODING FAILED! PLEASE CHECK YOUR URL!";
    }

    return [
        'cfgtype' => $base['scheme'] ?? '',
        'name' => $arrjs['ps'] ?? '',
        'host' => $arrjs['add'] ?? '',
        'port' => $arrjs['port'] ?? '',
        'uuid' => $arrjs['id'] ?? '',
        'alterId' => $arrjs['aid'] ?? '',
        'type' => $arrjs['net'] ?? '',
        'path' => $arrjs['path'] ?? '',
        'security' => $arrjs['type'] ?? '',
        'sni' => $arrjs['host'] ?? '',
        'tls' => $arrjs['tls'] ?? ''
    ];
}

function parseShadowsocks($basebuff, &$urlparsed) {
    $urlparsed['uuid'] = $basebuff['user'] ?? '';
    $basedata = explode(":", base64_decode($urlparsed['uuid']));
    if (count($basedata) == 2) {
        $urlparsed['cipher'] = $basedata[0];
        $urlparsed['uuid'] = $basedata[1];
    }
}

function parseUrl($basebuff) {
    $urlparsed = [
        'cfgtype' => $basebuff['scheme'] ?? '',
        'name' => $basebuff['fragment'] ?? '',
        'host' => $basebuff['host'] ?? '',
        'port' => $basebuff['port'] ?? ''
    ];

    if ($urlparsed['cfgtype'] == 'ss') {
        parseShadowsocks($basebuff, $urlparsed);
    } else {
        $urlparsed['uuid'] = $basebuff['user'] ?? '';
    }

    $querybuff = [];
    $tmpquery = $basebuff['query'] ?? '';

    if ($urlparsed['cfgtype'] == 'ss') {
        parse_str(str_replace(";", "&", $tmpquery), $querybuff);
        $urlparsed['mux'] = $querybuff['mux'] ?? '';
        $urlparsed['host2'] = $querybuff['host2'] ?? '';
    } else {
        parse_str($tmpquery, $querybuff);
    }

    $urlparsed['type'] = $querybuff['type'] ?? '';
    $urlparsed['path'] = $querybuff['path'] ?? '';
    $urlparsed['mode'] = $querybuff['mode'] ?? '';
    $urlparsed['plugin'] = $querybuff['plugin'] ?? '';
    $urlparsed['security'] = $querybuff['security'] ?? '';
    $urlparsed['encryption'] = $querybuff['encryption'] ?? '';
    $urlparsed['serviceName'] = $querybuff['serviceName'] ?? '';
    $urlparsed['sni'] = $querybuff['sni'] ?? '';

    return $urlparsed;
}

function generateConfig($data) {
    $outcfg = "";

    if (empty($GLOBALS['isProxiesPrinted'])) {
        $outcfg .= "proxies:\n";
        $GLOBALS['isProxiesPrinted'] = true;
    }

    switch ($data['cfgtype']) {
        case 'vless':
            $outcfg .= generateVlessConfig($data);
            break;
        case 'trojan':
            $outcfg .= generateTrojanConfig($data);
            break;
        case 'hysteria2':
        case 'hy2':
            $outcfg .= generateHysteria2Config($data);
            break;
        case 'ss':
            $outcfg .= generateShadowsocksConfig($data);
            break;
        case 'vmess':
            $outcfg .= generateVmessConfig($data);
            break;
    }

    return $outcfg;
}

function generateVlessConfig($data) {
    $config = "    - name: " . ($data['name'] ?: "VLESS") . "\n";
    $config .= "      type: {$data['cfgtype']}\n";
    $config .= "      server: {$data['host']}\n";
    $config .= "      port: {$data['port']}\n";
    $config .= "      uuid: {$data['uuid']}\n";
    $config .= "      cipher: auto\n";
    $config .= "      tls: true\n";
    if ($data['type'] == "ws") {
        $config .= "      network: ws\n";
        $config .= "      ws-opts:\n";
        $config .= "        path: {$data['path']}\n";
        $config .= "        Headers:\n";
        $config .= "          Host: {$data['host']}\n";
        $config .= "        flow:\n";
        $config .= "          client-fingerprint: chrome\n";
    } elseif ($data['type'] == "grpc") {
        $config .= "      network: grpc\n";
        $config .= "      grpc-opts:\n";
        $config .= "        grpc-service-name: {$data['serviceName']}\n";
    }
    $config .= "      udp: true\n";
    $config .= "      skip-cert-verify: true\n";
    return $config;
}

function generateTrojanConfig($data) {
    $config = "    - name: " . ($data['name'] ?: "TROJAN") . "\n";
    $config .= "      type: {$data['cfgtype']}\n";
    $config .= "      server: {$data['host']}\n";
    $config .= "      port: {$data['port']}\n";
    $config .= "      password: {$data['uuid']}\n";
    $config .= "      sni: " . (!empty($data['sni']) ? $data['sni'] : $data['host']) . "\n";
    if ($data['type'] == "ws") {
        $config .= "      network: ws\n";
        $config .= "      ws-opts:\n";
        $config .= "        path: {$data['path']}\n";
        $config .= "        Headers:\n";
        $config .= "          Host: {$data['sni']}\n";
    } elseif ($data['type'] == "grpc") {
        $config .= "      network: grpc\n";
        $config .= "      grpc-opts:\n";
        $config .= "        grpc-service-name: {$data['serviceName']}\n";
    }
    $config .= "      udp: true\n";
    $config .= "      skip-cert-verify: true\n";
    return $config;
}

function generateHysteria2Config($data) {
    return "    - name: " . ($data['name'] ?: "HYSTERIA2") . "\n" .
           "      server: {$data['host']}\n" .
           "      port: {$data['port']}\n" .
           "      type: {$data['cfgtype']}\n" .
           "      password: {$data['uuid']}\n" .
           "      udp: true\n" .
           "      ports: 20000-55000\n" .
           "      mport: 20000-55000\n" .
           "      skip-cert-verify: true\n" .
           "      sni: " . (!empty($data['sni']) ? $data['sni'] : $data['host']) . "\n";
}

function generateShadowsocksConfig($data) {
    $config = "    - name: " . ($data['name'] ?: "SHADOWSOCKS") . "\n";
    $config .= "      type: {$data['cfgtype']}\n";
    $config .= "      server: {$data['host']}\n";
    $config .= "      port: {$data['port']}\n";
    $config .= "      cipher: {$data['cipher']}\n";
    $config .= "      password: {$data['uuid']}\n";
    if (!empty($data['plugin'])) {
        $config .= "      plugin: {$data['plugin']}\n";
        $config .= "      plugin-opts:\n";
        if ($data['plugin'] == "v2ray-plugin" || $data['plugin'] == "xray-plugin") {
            $config .= "        mode: websocket\n";
            $config .= "        mux: {$data['mux']}\n";
        } elseif ($data['plugin'] == "obfs") {
            $config .= "        mode: tls\n";
        }
    }
    $config .= "      udp: true\n";
    $config .= "      skip-cert-verify: true\n";
    return $config;
}

function generateVmessConfig($data) {
    $config = "    - name: " . ($data['name'] ?: "VMESS") . "\n";
    $config .= "      type: {$data['cfgtype']}\n";
    $config .= "      server: {$data['host']}\n";
    $config .= "      port: {$data['port']}\n";
    $config .= "      uuid: {$data['uuid']}\n";
    $config .= "      alterId: {$data['alterId']}\n";
    $config .= "      cipher: auto\n";
    $config .= "      tls: " . ($data['tls'] === "tls" ? "true" : "false") . "\n";
    $config .= "      servername: " . (!empty($data['sni']) ? $data['sni'] : $data['host']) . "\n";
    $config .= "      network: {$data['type']}\n";
    if ($data['type'] == "ws") {
        $config .= "      ws-opts:\n";
        $config .= "        path: {$data['path']}\n";
        $config .= "        Headers:\n";
        $config .= "          Host: {$data['sni']}\n";
    } elseif ($data['type'] == "grpc") {
        $config .= "      grpc-opts:\n";
        $config .= "        grpc-service-name: {$data['serviceName']}\n";
    }
    $config .= "      udp: true\n";
    $config .= "      skip-cert-verify: true\n";
    return $config;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST['input'] ?? '';

    if (empty($input)) {
        echo ".";
    } else {
        $lines = explode("\n", trim($input));
        $allcfgs = "";
        $GLOBALS['isProxiesPrinted'] = false;

        foreach ($lines as $line) {
            $base64url = parse_url($line);
            if ($base64url === false) {
                $allcfgs .= "Invalid URL provided.\n";
                continue;
            }

            $base64url = array_map('urldecode', $base64url);

            if (isset($base64url['scheme']) && $base64url['scheme'] === 'vmess') {
                $parsedData = parseVmess($base64url);
            } else {
                $parsedData = parseUrl($base64url);
            }

            if (is_array($parsedData)) {
                $allcfgs .= generateConfig($parsedData);
            } else {
                $allcfgs .= $parsedData . "\n";
            }
        }

        $file_path = '/etc/neko/proxy_provider/subscription_7.json';
        file_put_contents($file_path, $allcfgs);

        echo "<h2 style=\"color: #00FFFF;\">Conversion Complete</h2>";
        echo "<p>Configuration file has been successfully saved to <strong>$file_path</strong></p>";
        echo "<textarea id='output' readonly style='width:100%;height:400px;'>$allcfgs</textarea>";
        echo "<button onclick='copyToClipboard()'>Copy</button>";
        echo "<script>
            function copyToClipboard() {
                var output = document.getElementById('output');
                output.select();
                document.execCommand('copy');
                alert('Copy successful');
            }
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>File Upload and Management</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            margin: 0;
            min-height: 100vh;
            align-items: center;
            justify-content: flex-start;
            color: #E0E0E0; 
            background-color: red;
            font-family: Arial, sans-serif;
            background: url('/nekoclash/assets/img/1.jpg') no-repeat center center fixed; 
            background-size: cover; 
        }
        .container {
            display: flex;
            flex-direction: column;
            width: 90%;
            max-width: 900px; 
            padding: 20px;
            box-sizing: border-box;
            align-items: center;
            text-align: center;
            background: rgba(30, 30, 30, 0.8); 
            border-radius: 10px;
            margin-top: 50px; 
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5); 
        }
        h1, h2, .help-text {
            color: #00FF7F; 
        }
        .form-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .form-inline .form-control-file {
            flex: 1;
        }
        .file-upload-button {
            padding: 10px 20px;
            background-color: #03DAC6; 
            color: #121212;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .file-upload-button:hover {
            background-color: #018786; 
        }
        .list-group {
            width: 100%;
            margin-top: 20px;
            padding: 0;
            list-style: none;
        }
        .list-group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: #2C2C2C; 
            border-bottom: 1px solid #444;
        }
        .list-group-item a {
            color: #BB86FC; 
            text-decoration: none;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
        .button-group form {
            display: inline;
        }
        .button-group .btn {
            margin-left: 5px;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }
        .btn-danger {
            background-color: #CF6679; 
            color: #121212;
        }
        .btn-danger:hover {
            background-color: #B00020; 
        }
        .btn-success {
            background-color: #03DAC6; 
            color: #121212;
        }
        .btn-success:hover {
            background-color: #018786; 
        }
        .btn-warning {
            background-color: #F4B400; 
            color: #121212;
        }
        .btn-warning:hover {
            background-color: #C79400; 
        }
        .editor {
            height: 300px; 
            width: 90%; 
            min-width: 800px; 
            max-width: 800px; 
            background-color: #2C2C2C; 
            color: #E0E0E0; 
            padding: 15px; 
            border: 1px solid #444;
            border-radius: 5px;
            font-family: monospace;
            margin-top: 20px;
            overflow: auto; 
        }
        .nav-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .nav-buttons .btn {
            padding: 10px 20px;
            background-color: #03DAC6; 
            color: #121212;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }
        .nav-buttons .btn:hover {
            background-color: #018786; 
        }
        .input-group {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
        }
        .input-group label {
            margin-right: 10px;
            white-space: nowrap;
            color: #00FF7F;
        }
        .input-group input {
            flex: 1;
            padding: 5px;
            border: 1px solid #444;
            border-radius: 5px;
            background-color: #2C2C2C;
            color: #E0E0E0;
        }
        button[name="update"] {
            background-color: #FF6347;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button[name="update"]:hover {
            background-color: darkgreen;
        }
        .form-spacing {
            margin-bottom: 30px;
        }
        button {
            background-color: #4CAF50; 
            color: white;
            border: none;
            padding: 5px 10px; 
            text-align: center; 
            text-decoration: none; 
            display: inline-block; 
            cursor: pointer; 
            border-radius: 4px; 
        }
        button:hover {
            background-color: darkgreen; 
        }

       .navigation {
           display: flex;
           justify-content: center; 
           gap: 10px; 
           margin-top: 20px;
       }

       .navigation .btn {
           padding: 12px 24px; 
           background-color: #03DAC6; 
           color: #121212; 
           border: none; 
           border-radius: 8px; 
           cursor: pointer; 
           text-decoration: none; 
           font-size: 16px; 
           box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); 
           display: inline-flex; 
           align-items: center; 
           justify-content: center; 
       }

       .navigation .btn:hover {
           background-color: #018786; 
           box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4); 
       }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1 class="text-primary">Mihomo File Manager</h1>

        <section id="proxy-management" class="section-gap">
            <h2 class="text-success">Proxy File Management</h2>
            <form action="" method="post" enctype="multipart/form-data" class="upload-form mb-3">
                <div class="input-group">
                    <input type="file" name="fileInput" id="fileInput" class="form-control-file">
                    <button type="submit" class="btn btn-primary btn-custom">Upload</button>
                </div>
            </form>
            <ul class="list-group list-group-flush">
                <?php foreach ($proxyFiles as $file): ?>
                    <?php $filePath = $uploadDir . $file; ?>
                    <li class="list-group-item">
                        <a href="download.php?file=<?php echo urlencode($file); ?>"><?php echo htmlspecialchars($file); ?></a>
                        <span class="file-size">(Size: <?php echo file_exists($filePath) ? formatSize(filesize($filePath)) : 'File does not exist'; ?>)</span>
                        <div class="button-group">
                            <form action="" method="post">
                                <input type="hidden" name="deleteFile" value="<?php echo htmlspecialchars($file); ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this file?');">Delete</button>
                            </form>
                            <form action="" method="post">
                                <input type="hidden" name="oldFileName" value="<?php echo htmlspecialchars($file); ?>">
                                <input type="text" name="newFileName" class="form-control form-control-sm" placeholder="New File Name" required>
                                <input type="hidden" name="fileType" value="proxy">
                                <button type="submit" class="btn btn-warning">Rename</button>
                            </form>
                            <form action="" method="post">
                                <input type="hidden" name="editFile" value="<?php echo htmlspecialchars($file); ?>">
                                <input type="hidden" name="fileType" value="proxy">
                                <button type="submit" class="btn btn-success">Edit</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <section id="config-management" class="section-gap">
            <h2 class="text-success">Configuration File Management</h2>
            <form action="" method="post" enctype="multipart/form-data" class="upload-form mb-3">
                <div class="input-group">
                    <input type="file" name="configFileInput" id="configFileInput" class="form-control-file">
                    <button type="submit" class="btn btn-primary btn-custom">Upload</button>
                </div>
            </form>
            <ul class="list-group list-group-flush">
                <?php foreach ($configFiles as $file): ?>
                    <?php $filePath = $configDir . $file; ?>
                    <li class="list-group-item">
                        <a href="download.php?file=<?php echo urlencode($file); ?>"><?php echo htmlspecialchars($file); ?></a>
                        <span class="file-size">(Size: <?php echo file_exists($filePath) ? formatSize(filesize($filePath)) : 'File does not exist'; ?>)</span>
                        <div class="button-group">
                            <form action="" method="post">
                                <input type="hidden" name="deleteConfigFile" value="<?php echo htmlspecialchars($file); ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this file?');">Delete</button>
                            </form>
                            <form action="" method="post">
                                <input type="hidden" name="oldFileName" value="<?php echo htmlspecialchars($file); ?>">
                                <input type="text" name="newFileName" class="form-control form-control-sm" placeholder="New File Name" required>
                                <input type="hidden" name="fileType" value="config">
                                <button type="submit" class="btn btn-warning">Rename</button>
                            </form>
                            <form action="" method="post">
                                <input type="hidden" name="editFile" value="<?php echo htmlspecialchars($file); ?>">
                                <input type="hidden" name="fileType" value="config">
                                <button type="submit" class="btn btn-success">Edit</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>

        <div class="navigation">
            <a href="javascript:history.back()" class="btn">Return to Previous Menu</a>
            <a href="/nekoclash/upload.php" class="btn">Return to Current Menu</a>
            <a href="/nekoclash/configs.php" class="btn">Return to Configuration Menu</a>
            <a href="/nekoclash" class="btn">Return to Main Menu</a>
        </div>

        <section id="custom-dir-upload" class="section-gap">
            <h2 class="text-success">Custom Directory File Upload</h2>
            <form action="" method="post" enctype="multipart/form-data" class="upload-form mb-3">
                <div class="input-group">
                    <input type="text" name="customDir" id="customDir" class="form-control" placeholder="Custom Directory" required>
                    <input type="file" name="customFileInput" id="customFileInput" class="form-control-file ml-2" required>
                    <button type="submit" class="btn btn-primary btn-custom">Upload to Custom Directory</button>
                </div>
            </form>
            <?php
            if (isset($_GET['customDir'])) {
                $customDir = rtrim($_GET['customDir'], '/') . '/';
                if (is_dir($customDir)) {
                    $customFiles = array_diff(scandir($customDir), array('.', '..'));
                    echo '<ul class="list-group list-group-flush">';
                    foreach ($customFiles as $file) {
                        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                        echo '<a href="?customDir=' . urlencode($customDir) . '&customFile=' . urlencode($file) . '">' . htmlspecialchars($file) . '</a>';
                        echo ' (Size: ' . formatSize(filesize($customDir . $file)) . ')';
                        echo '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<div class="alert alert-danger" role="alert">Directory does not exist!</div>';
                }
            }
            ?>
            <?php if (isset($fileContent)): ?>
                <?php $fileToEdit = ($_POST['fileType'] === 'proxy') ? $uploadDir . basename($_POST['editFile']) : $configDir . basename($_POST['editFile']); ?>
                <h2 style="color: #00FF7F;">Editing File: <?php echo $editingFileName; ?></h2>
                <p>Last Updated: <?php echo date('Y-m-d H:i:s', filemtime($fileToEdit)); ?></p>
                <form action="" method="post">
                    <textarea name="saveContent" rows="15" cols="150" class="editor"><?php echo $fileContent; ?></textarea><br>
                    <input type="hidden" name="fileName" value="<?php echo htmlspecialchars($_POST['editFile']); ?>">
                    <input type="hidden" name="fileType" value="<?php echo htmlspecialchars($_POST['fileType']); ?>">
                    <input type="submit" value="Save Content">
                </form>
            <?php endif; ?>
        </section>

        <section id="subscription-management" class="section-gap">
            <h2 class="text-success">Subscription Management</h2>
            <p class="help-text" style="text-align: left; font-family: Arial, sans-serif; line-height: 1.5; font-size: 14px;">
                <strong>1. Note:</strong> The universal template (<code>tuanbe.yaml</code>) supports up to <strong>7</strong> subscription links. Please do not change the default names.
            </p>

            <p class="help-text" style="text-align: left; font-family: Arial, sans-serif; line-height: 1.5; font-size: 14px;">
                <strong>2. Save and Update:</strong> After filling out, please click the “Update Configuration” button to save.
            </p>

            <p class="help-text" style="text-align: left; font-family: Arial, sans-serif; line-height: 1.5; font-size: 14px;">
                <strong>3. Node Conversion and Manual Modification:</strong> This template supports all formats of subscription links without additional conversion. Individual nodes can be converted using the node conversion tool below and automatically saved as proxies, or the proxy directory file can be manually modified to add nodes via link format.
            </p>
            <div class="form-spacing"></div>
            <?php if ($message): ?>
                <p><?php echo nl2br(htmlspecialchars($message)); ?></p>
            <?php endif; ?>
            <?php for ($i = 0; $i < 7; $i++): ?>
                <form method="post" class="mb-3">
                    <div class="input-group">
                        <label for="subscription_url_<?php echo $i; ?>" class="sr-only">Subscription Link <?php echo ($i + 1); ?>:</label>
                        <input type="text" name="subscription_url" id="subscription_url_<?php echo $i; ?>" value="<?php echo htmlspecialchars($subscriptions[$i]['url']); ?>" required class="form-control">
                        <input type="text" name="custom_file_name" id="custom_file_name_<?php echo $i; ?>" value="<?php echo htmlspecialchars($subscriptions[$i]['file_name']); ?>" class="form-control ml-2" placeholder="Custom File Name">
                        <input type="hidden" name="index" value="<?php echo $i; ?>">
                        <button type="submit" name="update" class="btn btn-primary btn-custom ml-2">Update Configuration</button>
                    </div>
                </form>
            <?php endfor; ?>
        </section>

        <section id="base64-conversion" class="section-gap">
            <h2 class="text-success">Base64 Node Information Conversion</h2>
            <form method="post">
                <div class="input-group form-spacing">
                    <label for="base64_content" class="sr-only">Base64 Content:</label>
                    <textarea name="base64_content" id="base64_content" rows="4" class="form-control" required></textarea>
                    <button type="submit" name="convert_base64" class="btn btn-primary btn-custom ml-2">Generate Node Information</button>
                </div>
            </form>
        </section>
        
        <h1 style="color: #00FF7F;">Node Conversion Tool</h1>
        <form method="post">
            <textarea name="input" rows="10" cols="50" placeholder="Paste ss//vless//vmess//trojan//hysteria2 node information..."></textarea>
            <button type="submit" name="convert">Convert</button>
        </form>
    </div>
</body>
</html>