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
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mihomo File Manager</title>
    <link href="./assets/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #87ceeb;
            background-size: cover;
            color: #E0E0E0;
        }

        .container {
            background: rgba(30, 30, 30, 0.8);
            border-radius: 10px;
            padding: 20px;
            margin-top: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }

        h1,
        h2 {
            color: #00FF7F;
        }

        .table th,
        .table td {
            text-align: center;
            vertical-align: middle;
        }

        .editor {
            height: 300px;
            width: 100%;
            background-color: #2C2C2C;
            color: #E0E0E0;
            padding: 15px;
            border: 1px solid #444;
            border-radius: 5px;
            font-family: monospace;
            margin-top: 20px;
            overflow: auto;
        }

        .btn-warning {
            background-color: #F4B400;
            color: #FFFFFF;
        }

        .btn-warning:hover {
            background-color: #C79400;
            color: #FFFFFF;
        }

        .table-custom th,
        .table-custom td {
            padding: 10px;
        }

        .table-custom .file-name-col {
            width: 40%;
        }

        .table-custom .size-col {
            width: 20%;
        }

        .table-custom .date-col {
            width: 20%;
        }

        .table-custom .actions-col {
            width: 20%;
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: center;
        }

        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .btn-group .btn {
            flex: 1;
        }

        .table-custom {
            width: 100%;
            table-layout: fixed;
        }

        .size-column {
            width: 100px;
            text-align: center;
        }

        .action-column {
            width: 400px;
        }

        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .btn-group form,
        .btn-group button {
            margin: 0;
        }

        .table-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            margin-bottom: 40px;
        }

        .modal-header,
        .modal-body,
        .modal-footer {
            background: #f8f9fa;
            color: #333;
        }

        .modal-content {
            background: #ffffff;
            border: 1px solid #ced4da;
        }

        .modal-body {
            overflow-y: auto;
        }

        .form-control {
            background-color: #2C2C2C;
            color: #E0E0E0;
            border: 1px solid #444;
        }

        .form-control:focus {
            border-color: #03DAC6;
            box-shadow: 0 0 0 0.2rem rgba(3, 218, 198, 0.25);
        }

        .log-output {
            background-color: #2C2C2C;
            border: 1px solid #444;
            border-radius: 5px;
            color: #E0E0E0;
            padding: 10px;
            margin-top: 20px;
            height: 200px;
            overflow-y: scroll;
            white-space: pre-wrap;
        }

        .subscription-card {
            background: #3C3C3C;
            border: 1px solid #444;
            color: #E0E0E0;
            margin-bottom: 20px;
        }

        .subscription-card .card-body {
            padding: 10px;
        }

        .custom-file-name {
            background-color: #2C2C2C;
            color: #E0E0E0;
            border: 1px solid #444;
        }

        .card .form-control {
            background-color: #2C2C2C;
            color: #E0E0E0;
            border: 1px solid #444;
        }

        .card .form-control:focus {
            border-color: #03DAC6;
            box-shadow: 0 0 0 0.2rem rgba(3, 218, 198, 0.25);
        }

        .form-inline .form-control-file {
            display: none;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-group .btn {
            height: 38px;
            line-height: 1.5;
            padding: 0 10px;
            text-align: center;
        }

        .upload-btn {
            cursor: pointer;
        }

        .btn-group .btn-rename {
            max-width: 80px;
            padding: 2px 6px;
            font-size: 0.875rem;
            width: auto;
            white-space: nowrap;
            border-radius: 4px !important;
            color: #FFFFFF;
        }

        @media (max-width: 768px) {
            .btn-group {
                flex-direction: column;
            }

            .btn-group .btn {
                width: 100%;
                margin-bottom: 5px;
            }

            .nav-buttons {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .nav-buttons .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1 style="margin-top: 40px; margin-bottom: 20px;">Mihomo File Manager</h1>

        <div class="table-wrapper">
            <h2>Proxy File Management</h2>
            <table class="table table-dark table-bordered table-custom">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th class="size-column">Size</th>
                        <th>Modification Time</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proxyFiles as $file): ?>
                        <?php $filePath = $uploadDir . $file; ?>
                        <tr>
                            <td><a href="download.php?file=<?php echo urlencode($file); ?>"><?php echo htmlspecialchars($file); ?></a></td>
                            <td class="size-column"><?php echo file_exists($filePath) ? formatSize(filesize($filePath)) : 'File not found'; ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', filemtime($filePath))); ?></td>
                            <td class="action-column">
                                <div class="btn-group">
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="deleteFile" value="<?php echo htmlspecialchars($file); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this file?');">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    
                                    <button type="button" class="btn btn-success btn-sm btn-rename" data-toggle="modal" data-target="#renameModal" data-filename="<?php echo htmlspecialchars($file); ?>" data-filetype="proxy">
                                        <i class="fas fa-edit"></i> Rename
                                    </button>
                                    
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="editFile" value="<?php echo htmlspecialchars($file); ?>">
                                        <input type="hidden" name="fileType" value="proxy"> 
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="fas fa-pen"></i> Edit
                                        </button>
                                    </form>

                                    <form action="" method="post" enctype="multipart/form-data" class="form-inline d-inline upload-btn">
                                        <input type="file" name="fileInput" class="form-control-file" required id="fileInput-<?php echo htmlspecialchars($file); ?>" onchange="this.form.submit()">
                                        <button type="button" class="btn btn-info" onclick="document.getElementById('fileInput-<?php echo htmlspecialchars($file); ?>').click();">
                                            <i class="fas fa-upload"></i> Upload
                                        </button>  
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="modal fade" id="renameModal" tabindex="-1" role="dialog" aria-labelledby="renameModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="renameModalLabel">Rename File</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="renameForm" action="" method="post">
                            <input type="hidden" name="oldFileName" id="oldFileName">
                            <input type="hidden" name="fileType" id="fileType">
                            <div class="form-group">
                                <label for="newFileName">Rename File</label>
                                <input type="text" class="form-control" id="newFileName" name="newFileName" required>
                            </div>
                            <p>Are you sure you want to rename this file?</p>
                            <div class="form-group text-right">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Confirm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <h2>Configuration File Management</h2>
            <table class="table table-dark table-bordered table-custom">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th class="size-column">Size</th>
                        <th>Modification Time</th>
                        <th class="action-column">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($configFiles as $file): ?>
                        <?php $filePath = $configDir . $file; ?>
                        <tr>
                            <td><a href="download.php?file=<?php echo urlencode($file); ?>"><?php echo htmlspecialchars($file); ?></a></td>
                            <td class="size-column"><?php echo file_exists($filePath) ? formatSize(filesize($filePath)) : 'File not found'; ?></td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i:s', filemtime($filePath))); ?></td>
                            <td class="action-column">
                                <div class="btn-group">
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="deleteConfigFile" value="<?php echo htmlspecialchars($file); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this file?');"><i class="fas fa-trash"></i> Delete</button>                                     
                                    </form>
                                    <button type="button" class="btn btn-success btn-sm btn-rename" data-toggle="modal" data-target="#renameModal" data-filename="<?php echo htmlspecialchars($file); ?>" data-filetype="config"><i class="fas fa-edit"></i> Rename</button>
                                   
                                    <form action="" method="post" class="d-inline">
                                        <input type="hidden" name="editFile" value="<?php echo htmlspecialchars($file); ?>">
                                        <input type="hidden" name="fileType" value="config">
                                        <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i> Edit</button>    
                                    </form>
                                    <form action="" method="post" enctype="multipart/form-data" class="form-inline d-inline upload-btn">
                                        <input type="file" name="configFileInput" class="form-control-file" required id="fileInput-<?php echo htmlspecialchars($file); ?>" onchange="this.form.submit()">
                                        <button type="button" class="btn btn-info" onclick="document.getElementById('fileInput-<?php echo htmlspecialchars($file); ?>').click();"><i class="fas fa-upload"></i> Upload</button>                                  
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if (isset($fileContent)): ?>
                <?php if (isset($_POST['editFile'])): ?>
                    <?php $fileToEdit = ($_POST['fileType'] === 'proxy') ? $uploadDir . basename($_POST['editFile']) : $configDir . basename($_POST['editFile']); ?>
                    <h2 class="mt-5">Edit File: <?php echo $editingFileName; ?></h2>
                    <p>Last Updated Date: <?php echo date('Y-m-d H:i:s', filemtime($fileToEdit)); ?></p>
                    <div class="editor-container">
                        <form action="" method="post">
                            <textarea name="saveContent" id="editor" class="editor"><?php echo $fileContent; ?></textarea><br>
                            <input type="hidden" name="fileName" value="<?php echo htmlspecialchars($_POST['editFile']); ?>">
                            <input type="hidden" name="fileType" value="<?php echo htmlspecialchars($_POST['fileType']); ?>">
                            <button type="submit" class="btn btn-primary mt-2" onclick="checkJsonSyntax()"><i class="fas fa-save"></i>  Save Content</button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
<div class="navigation">
    <a href="javascript:history.back()" class="btn btn-success">Return to Previous Menu</a>
    <a href="/nekoclash/upload.php" class="btn btn-success">Return to Current Menu</a>
    <a href="/nekoclash/configs.php" class="btn btn-success">Return to Configuration Menu</a>
    <a href="/nekoclash" class="btn btn-success">Return to Main Menu</a>
</div>
        <section id="subscription-management" class="section-gap">
            <h2 class="text-success"  style="margin-top: 20px; margin-bottom: 20px;">Subscription Management</h2>
                   <button id="pasteButton" class="btn btn-primary">Generate Subscription Link Website</button>
                   <button id="base64Button" class="btn btn-primary">Base64 Online Encoder and Decoder</button>
                <p class="help-text" style="text-align: left; font-family: Arial, sans-serif; line-height: 1.5; font-size: 14px;">
                <strong>1. Note:</strong> The universal template (<code>mihomo.yaml</code>) supports up to <strong>7</strong> subscription links. Please do not change the default names.   
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
                        <button type="submit" name="update" class="btn btn-primary btn-custom ml-2"><i class="fas fa-sync-alt"></i>Update Configuration</button>
                    </div>
                </form>
            <?php endfor; ?>
        </section>

      <section id="base64-conversion" class="section-gap">
            <h2 class="text-success">Base64 Node Information Conversion</h2>
            <form method="post">
                <div class="form-group">
                    <textarea name="base64_content" id="base64_content" rows="4" class="form-control" placeholder="Paste Base64 content..."" required></textarea>
                </div>
                <button type="submit" name="convert_base64" class="btn btn-primary btn-custom">Generate Node Information</button>
            </form>
        </section>

        <section id="node-conversion" class="section-gap">
            <h1 class="text-success">Node Conversion Tool</h1>
            <form method="post">
                <div class="form-group">
                    <textarea name="input" rows="10" class="form-control" placeholder="Paste ss//vless//vmess//trojan//hysteria2 node information..."></textarea>
                </div>
                <button type="submit" name="convert" class="btn btn-primary">Convert</button>
            </form>
        </section>
    </div>

    <script src="./assets/bootstrap/jquery-3.5.1.slim.min.js"></script>
    <script src="./assets/bootstrap/popper.min.js"></script>
    <script src="./assets/bootstrap/bootstrap.min.js"></script>
        <script>
             document.getElementById('pasteButton').onclick = function() {
                 window.open('https://paste.gg', '_blank');
             }
             document.getElementById('base64Button').onclick = function() {
                 window.open('https://base64.us', '_blank');
             }
        </script>
        <script>
            $('#renameModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); 
                var oldFileName = button.data('filename'); 
                var fileType = button.data('filetype');
                var modal = $(this);
                modal.find('#oldFileName').val(oldFileName); 
                modal.find('#fileType').val(fileType);
                modal.find('#newFileName').val(oldFileName); 
            });
        </script>
</body>
</html>
