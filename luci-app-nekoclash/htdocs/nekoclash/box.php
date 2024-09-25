<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>订阅转换模板</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/all.min.css">
    <style>
        body {
            background-color: #87ceeb;
            margin: 0;
            padding: 0;
        }
        .outer-container {
            padding: 20px;
            min-height: 100vh;
        }
        .container {
            background-color: #fff;
            color: #333;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 50px;
        }
        .textarea-container {
            height: 200px;
            overflow-y: auto;
        }
        .clear-button {
            background-color: #ff4c4c;
            color: white;
        }
        .clear-button:hover {
            background-color: #ff1a1a;
        }
        .result-container, .log-container, .saved-data-container {
            margin-top: 20px;
        }
        .result-container textarea {
            height: 100%;
            resize: vertical;
        }
        .saved-data-container pre {
            word-wrap: break-word;
            white-space: pre-wrap;
        }
        .log-container {
            max-height: 200px;
            overflow-y: auto;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
  <div class="outer-container">
        <div class="container">
            <h1 class="text-center text-primary">Sing-box 订阅转换模板</h1>
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">帮助信息</h4>
                <p>请选择一个模板以生成配置文件：根据订阅节点信息选择对应模板，否则启动不了。</p>
                <ul>
                    <li><strong>默认模板 1</strong>：香港 台湾 新加坡 日本 美国 韩国。</li>
                    <li><strong>默认模板 2</strong>：新加坡 日本 美国 韩国。</li>
                    <li><strong>默认模板 3</strong>：香港 新加坡 日本 美国。</li>
                    <li><strong>默认模板 4</strong>：香港 日本 美国。</li>
                    <li><strong>默认模板 5</strong>：无地区 通用。</li>
                </ul>
            </div>
            <form method="post" action="">
                <div class="form-group">
                    <label for="subscribeUrl">订阅链接地址:</label>
                    <input type="text" class="form-control" id="subscribeUrl" name="subscribeUrl" required>
                </div>
                <fieldset class="form-group">
                    <legend>选择模板</legend>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" id="useDefaultTemplate" name="templateOption" value="default" checked>
                        <label class="form-check-label" for="useDefaultTemplate">使用默认模板</label>
                    </div>
                    <div class="form-row">
                        <div class="col-sm">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="useDefaultTemplate1" name="defaultTemplate" value="mixed" checked>
                                <label class="form-check-label" for="useDefaultTemplate1">默认模板 1</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="useDefaultTemplate2" name="defaultTemplate" value="second">
                                <label class="form-check-label" for="useDefaultTemplate2">默认模板 2</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="useDefaultTemplate3" name="defaultTemplate" value="fakeip">
                                <label class="form-check-label" for="useDefaultTemplate3">默认模板 3</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="useDefaultTemplate4" name="defaultTemplate" value="tun">
                                <label class="form-check-label" for="useDefaultTemplate4">默认模板 4</label>
                            </div>
                        </div>
                        <div class="col-sm">
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="useDefaultTemplate5" name="defaultTemplate" value="ip">
                                <label class="form-check-label" for="useDefaultTemplate5">默认模板 5</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" id="useCustomTemplate" name="templateOption" value="custom">
                        <label class="form-check-label" for="useCustomTemplate">使用自定义模板URL:</label>
                        <input type="text" class="form-control" id="customTemplateUrl" name="customTemplateUrl" placeholder="输入自定义模板URL">
                    </div>
                </fieldset>
                <div class="form-group text-center">
                    <input type="submit" name="generateConfig" class="btn btn-primary" value="生成配置文件">
                </div>
            </form>

            <?php

$dataFilePath = '/tmp/subscription_data.txt';
$configFilePath = '/etc/neko/config/config.json';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['generateConfig'])) {
    $subscribeUrl = trim($_POST['subscribeUrl']);
    $customTemplateUrl = trim($_POST['customTemplateUrl']);

    $dataContent = "订阅链接地址: " . $subscribeUrl . "\n" . "自定义模板URL: " . $customTemplateUrl . "\n";
    file_put_contents($dataFilePath, $dataContent, FILE_APPEND);

    $subscribeUrlEncoded = urlencode($subscribeUrl);

    if ($_POST['templateOption'] === 'custom' && !empty($customTemplateUrl)) {
        $templateUrlEncoded = urlencode($customTemplateUrl);
    } elseif ($_POST['templateOption'] === 'default') {
        switch ($_POST['defaultTemplate']) {
            case 'mixed':
                $templateUrlEncoded = urlencode("https://raw.githubusercontent.com/Thaolga/Rules/main/Clash/json/config_mixed.json");
                break;
            case 'second':
                $templateUrlEncoded = urlencode("https://raw.githubusercontent.com/Thaolga/Rules/main/Clash/json/config.json");
                break;
            case 'fakeip':
                $templateUrlEncoded = urlencode("https://raw.githubusercontent.com/Thaolga/Rules/main/Clash/json/config_fakeip.json");
                break;
            case 'tun':
                $templateUrlEncoded = urlencode("https://raw.githubusercontent.com/Thaolga/Rules/main/Clash/json/config_tun.json");
                break;
            case 'ip':
                $templateUrlEncoded = urlencode("https://raw.githubusercontent.com/Thaolga/Rules/main/Clash/json/config_ip.json");
                break;
            default:
                $templateUrlEncoded = urlencode("https://raw.githubusercontent.com/Thaolga/Rules/main/Clash/json/config_mixed.json");
                break;
        }
    }

    $completeSubscribeUrl = "https://sing-box-subscribe-doraemon.vercel.app/config/{$subscribeUrlEncoded}&file={$templateUrlEncoded}";
    $tempFilePath = '/tmp/config.json';

    $command = "wget -O " . escapeshellarg($tempFilePath) . " " . escapeshellarg($completeSubscribeUrl);
    exec($command, $output, $returnVar);

    $logMessages = [];

    if ($returnVar !== 0) {
        $logMessages[] = "无法下载内容: " . htmlspecialchars($completeSubscribeUrl);
    } else {
        $downloadedContent = file_get_contents($tempFilePath);
        if ($downloadedContent === false) {
            $logMessages[] = "无法读取下载的文件内容";
        } else {
            if (file_put_contents($configFilePath, $downloadedContent) === false) {
                $logMessages[] = "无法保存修改后的内容到: " . $configFilePath;
            } else {
                $logMessages[] = "配置文件生成并保存成功: " . $configFilePath;
                $logMessages[] = "生成并下载的订阅URL: <a href='" . htmlspecialchars($completeSubscribeUrl) . "' target='_blank'>" . htmlspecialchars($completeSubscribeUrl) . "</a>";
            }
        }
    }

    echo "<div class='result-container'>";
    echo "<form method='post' action=''>";
    echo "<div class='form-group textarea-container'>";
    echo "<textarea id='configContent' name='configContent' class='form-control'>" . htmlspecialchars($downloadedContent) . "</textarea>";
    echo "</div>";
    echo "<div class='form-group text-center'>";
    echo "<button class='btn btn-info' type='button' onclick='copyToClipboard()'><i class='fas fa-copy'></i> 复制到剪贴</button>";
    echo "<input type='hidden' name='saveContent' value='1'>";
    echo "<button class='btn btn-success' type='submit'>保存修改</button>";
    echo "</div>";
    echo "</form>";
    echo "</div>";

    echo "<div class='log-container alert alert-info'>";
    foreach ($logMessages as $message) {
        echo $message . "<br>";
    }
    echo "</div>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['saveContent'])) {
    if (isset($_POST['configContent'])) {
        $editedContent = trim($_POST['configContent']);
        if (file_put_contents($configFilePath, $editedContent) === false) {
            echo "<div class='log-container alert alert-danger'>无法保存修改后的内容到: " . htmlspecialchars($configFilePath) . "</div>";
        } else {
            echo "<div class='log-container alert alert-success'>内容已成功保存到: " . htmlspecialchars($configFilePath) . "</div>";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clearData'])) {
    if (file_exists($dataFilePath)) {
        file_put_contents($dataFilePath, '');
        echo "<div class='log-container alert alert-success'>保存的数据已清空。</div>";
    }
}

if (file_exists($dataFilePath)) {
    $savedData = file_get_contents($dataFilePath);
    echo "<div class='card saved-data-container'>";
    echo "<div class='card-body'>";
    echo "<h2 class='card-title'>保存的数据</h2>";
    echo "<pre>" . htmlspecialchars($savedData) . "</pre>";
    echo "<form method='post' action=''>";
    echo "<button class='btn clear-button' type='submit' name='clearData'>清空数据</button>";
    echo "</form>";
    echo "</div>";
    echo "</div>";
}
            ?>

            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script>
                function copyToClipboard() {
                    const copyText = document.getElementById("configContent");
                    copyText.select();
                    document.execCommand("copy");
                    alert("已复制到剪贴板");
                }
            </script>
        </div>
    </div>
</body>

</html>
