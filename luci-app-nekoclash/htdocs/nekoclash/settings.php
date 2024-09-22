<?php

include './cfg.php';
include './video.php';

$themeDir = "$neko_www/assets/theme";
$tmpPath = "$neko_www/lib/selected_config.txt";
$arrFiles = array();
$arrFiles = glob("$themeDir/*.css");

for($x=0;$x<count($arrFiles);$x++) $arrFiles[$x] = substr($arrFiles[$x], strlen($themeDir)+1);

if(isset($_POST['themechange'])){
    $dt = $_POST['themechange'];
    shell_exec("echo $dt > $neko_www/lib/theme.txt");
    $neko_theme = $dt;
}
if(isset($_POST['fw'])){
    $dt = $_POST['fw'];
    if ($dt == 'enable') shell_exec("uci set neko.cfg.new_interface='1' && uci commit neko");
    if ($dt == 'disable') shell_exec("uci set neko.cfg.new_interface='0' && uci commit neko");
}
$fwstatus=shell_exec("uci get neko.cfg.new_interface");
?>
<?php
function getSingboxVersion() {
    $singBoxPath = '/usr/bin/sing-box'; 
    $command = "$singBoxPath version 2>&1";
    exec($command, $output, $returnVar);
    
    if ($returnVar === 0) {
        foreach ($output as $line) {
            if (strpos($line, 'version') !== false) {
                $parts = explode(' ', $line);
                return end($parts);
            }
        }
    }
    
    return '未知版本';
}

$singBoxVersion = getSingboxVersion();
?>
<!doctype html>
<html lang="en" data-bs-theme="<?php echo substr($neko_theme,0,-4) ?>">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings - Neko</title>
    <link rel="icon" href="./assets/img/favicon.png">
    <link href="./assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="./assets/theme/<?php echo $neko_theme ?>" rel="stylesheet">
    <link href="./assets/css/custom.css" rel="stylesheet">
    <script type="text/javascript" src="./assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="./assets/js/feather.min.js"></script>
    <script type="text/javascript" src="./assets/js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="./assets/js/neko.js"></script>
  </head>
  <body>
    <div class="container-sm container-bg text-center callout border border-3 rounded-4 col-11">
        <div class="row">
            <a href="./" class="col btn btn-lg">首页</a>
            <a href="./dashboard.php" class="col btn btn-lg">面板</a>
            <a href="./configs.php" class="col btn btn-lg">配置</a>
            <a href="#" class="col btn btn-lg">设定</a>
        </div>
    </div>
<div class="container text-left p-3">
    <div class="container container-bg border border-3 rounded-4 col-12 mb-4">
        <h2 class="text-center p-2 mb-3">主题设定</h2>
        <form action="settings.php" method="post">
            <div class="container text-center justify-content-md-center">
                <div class="row justify-content-md-center">
                    <div class="col mb-3 justify-content-md-center">
                        <select class="form-select" name="themechange" aria-label="themex">
                            <option selected>Change Theme (<?php echo $neko_theme ?>)</option>
                            <?php foreach ($arrFiles as $file) echo "<option value=\"".$file.'">'.$file."</option>" ?>
                        </select>
                    </div>
                    <div class="row justify-content-md-center">
                        <div class="col justify-content-md-center mb-3">
                            <input class="btn btn-info" type="submit" value="更改主题">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <h2 class="text-center p-2 mb-3">软体资讯</h2>
        <table class="table table-borderless mb-3">
            <tbody>
                <tr>
                    <td colspan="2">
                        <h3 class="text-center mb-3">自动重载防火墙</h3>
                        <form action="settings.php" method="post">
                            <div class="btn-group d-flex justify-content-center">
                                <button type="submit" name="fw" value="enable" class="btn btn<?php if($fwstatus==1) echo "-outline" ?>-success <?php if($fwstatus==1) echo "disabled" ?>">启用</button>
                                <button type="submit" name="fw" value="disable" class="btn btn<?php if($fwstatus==0) echo "-outline" ?>-danger <?php if($fwstatus==0) echo "disabled" ?>">停用</button>
                            </div>
                        </form>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <h3>客户端版本</h3>
                                    <div class="form-control text-center" style="font-family: monospace; text-align: center;">
                                        <div id="cliver"></div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <button class="btn btn-primary" id="updateButton">更新到最新版本</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <h3>Sing-box核心版本</h3>
                                    <div class="form-control text-center">
                                        <div id="singBoxCorever">
                                            <?php echo htmlspecialchars($singBoxVersion); ?>
                                        </div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <button class="btn btn-pink" id="updateSingboxButton">更新 Singbox 内核</button>
                                        <button class="btn btn-info" id="updatePuernyaButton">切换 Puernya 内核</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="text-center">
                                    <h3>Mihomo核心版本</h3>
                                    <div class="form-control text-center">
                                        <div id="corever">-</div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <button class="btn btn-success" id="updateCoreButton">更新 Mihomo 内核</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div id="logOutput" class="mt-3"></div>

        <style>
            .table-container {
                overflow-x: auto;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
            }

            .table td {
                padding: 10px;
                word-wrap: break-word;
            }

            .form-control {
                width: 100%;
            }

            .btn {
                white-space: nowrap;
                flex: 1;
            }

            @media (max-width: 767px) {
                .table td {
                    display: block;
                    width: 100%;
                }

                .form-control {
                    display: flex;
                    flex-direction: column;
                }

                .btn-group {
                    flex-direction: column;
                }
            }

            #updateButton:hover {
                background-color: #20B2AA;
            }

            #updateSingboxButton:hover {
                background-color: #FF69B4;
            }

            #updateCoreButton:hover {
                background-color: #90EE90;
            }

            #updatePuernyaButton:hover {
                background-color: #87CEFA;
            }
        </style>

        <script>
            document.getElementById('updateButton').addEventListener('click', function() {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'update_script.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                document.getElementById('logOutput').innerHTML = '开始下载更新...';

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('logOutput').innerHTML += '\n更新完成！';
                        document.getElementById('logOutput').innerHTML += '\n' + xhr.responseText;
                    } else {
                        document.getElementById('logOutput').innerHTML += '\n发生错误：' + xhr.statusText;
                    }
                };

                xhr.send();
            });

            document.getElementById('updateSingboxButton').addEventListener('click', function() {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'singbox.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                document.getElementById('logOutput').innerHTML = '开始下载核心更新...';

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('logOutput').innerHTML += '\n核心更新完成！';
                        document.getElementById('logOutput').innerHTML += '\n' + xhr.responseText;
                    } else {
                        document.getElementById('logOutput').innerHTML += '\n发生错误：' + xhr.statusText;
                    }
                };

                xhr.send();
            });

            document.getElementById('updatePuernyaButton').addEventListener('click', function() {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'puernya.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                document.getElementById('logOutput').innerHTML = '开始下载核心更新...';

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('logOutput').innerHTML += '\n核心更新完成！';
                        document.getElementById('logOutput').innerHTML += '\n' + xhr.responseText;
                    } else {
                        document.getElementById('logOutput').innerHTML += '\n发生错误：' + xhr.statusText;
                    }
                };

                xhr.send();
            });


            document.getElementById('updateCoreButton').addEventListener('click', function() {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'core.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                document.getElementById('logOutput').innerHTML = '开始下载核心更新...';

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('logOutput').innerHTML += '\n核心更新完成！';
                        document.getElementById('logOutput').innerHTML += '\n' + xhr.responseText;
                    } else {
                        document.getElementById('logOutput').innerHTML += '\n发生错误：' + xhr.statusText;
                    }
                };

                xhr.send();
            });
        </script>
     <div class="feature-box text-center">
      <h2>语音播报系统</h2>
        <tbody>
            <tr>
                <td>
                    <div class="row mb-2" style="margin-bottom: 5px;">
                        <div class="col">
                            <input type="text" id="city-input" class="form-control" placeholder="如 Beijing" style="padding: 5px;">
                        </div>
                        <div class="col-auto">
                            <button onclick="saveCity()" class="btn btn-success" style="padding: 5px 10px;">保存城市</button
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
 </div>
     </div>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NekoClash</title>
    <link rel="stylesheet" href="/www/nekoclash/assets/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .feature-box {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #000000;
            border-radius: 8px;
        }
        .feature-box h6 {
            margin-bottom: 15px;
        }
        .table-container {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #000000;
            border-radius: 8px;
        }
        .table {
            table-layout: fixed;
            width: 100%;
        }
        .table td, .table th {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .table thead th {
            background-color: transparent;
            color: #000000;
        }
        .btn-outline-secondary {
            border-color: transparent;
            color: #000000;
        }
        .btn-outline-secondary:hover {
            background-color: transparent;
            color: #000000;
        }
        .footer {
            padding: 15px 0;
            background-color: transparent;
            color: #000000;
        }
        .footer p {
            margin: 0;
        }
        .link-box {
            border: 1px solid #000000;
            border-radius: 8px;
            padding: 10px;
            display: block;
            text-align: center;
            width: 100%;
            box-sizing: border-box; 
            transition: background-color 0.3s ease; 
        }
        .link-box a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #000000;
        }
        .link-box:hover {
            background-color: #EE82EE; 
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">关于 NekoClash</h2>
        <div class="feature-box text-center">
            <h5>NekoClash</h5>
            <p>NekoClash 是一款精心设计的 Mihomo 代理工具，专为家庭用户打造，旨在提供简洁而强大的代理解决方案。基于 PHP 和 BASH 技术，NekoClash 将复杂的代理配置简化为直观的操作体验，让每个用户都能轻松享受高效、安全的网络环境。</p>
        </div>

        <h5 class="text-center mb-4">核心特点</h5>
        <div class="row">
            <div class="col-md-4 mb-4 d-flex">
                <div class="feature-box text-center flex-fill">
                    <h6>简化配置</h6>
                    <p>采用用户友好的界面和智能配置功能，轻松实现 Mihomo 代理的设置与管理。</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 d-flex">
                <div class="feature-box text-center flex-fill">
                    <h6>优化性能</h6>
                    <p>通过高效的脚本和自动化处理，确保最佳的代理性能和稳定性。</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 d-flex">
                <div class="feature-box text-center flex-fill">
                    <h6>无缝体验</h6>
                    <p>专为家庭用户设计，兼顾易用性与功能性，确保每个家庭成员都能便捷地使用代理服务。</p>
                </div>
            </div>
        </div>

<h5 class="text-center mb-4">工具信息</h5>
<div class="d-flex justify-content-center">
    <div class="table-container">
        <table class="table table-borderless mb-5">
            <tbody>
                <tr class="text-center">
                    <td>SagerNet</td>
                    <td>MetaCubeX</td>
                </tr>
                <tr class="text-center">
                    <td>
                        <div class="link-box">
                            <a href="https://github.com/SagerNet/sing-box" target="_blank">Sing-box</a>
                        </div>
                    </td>
                    <td>
                        <div class="link-box">
                            <a href="https://github.com/MetaCubeX/mihomo" target="_blank">Mihomo</a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
    <h5 class="text-center mb-4">外部链接</h5>
        <div class="table-container">
            <table class="table table-borderless mb-5">
                <tbody>
                    <tr class="text-center">
                        <td>Github</td>
                        <td>Github</td>
                    </tr>
                    <tr class="text-center">
                        <td>
                            <div class="link-box">
                                <a href="https://github.com/Thaolga/luci-app-nekoclash/issues" target="_blank">Issues</a>
                            </div>
                        </td>
                        <td>
                            <div class="link-box">
                                <a href="https://github.com/Thaolga/luci-app-nekoclash" target="_blank">Thaolga</a>
                            </div>
                        </td>
                    </tr>
                    <tr class="text-center">
                        <td>Telegram</td>
                        <td>MetaCubeX</td>
                    </tr>
                    <tr class="text-center">
                        <td>
                            <div class="link-box">
                                <a href="https://t.me/+J55MUupktxFmMDgx" target="_blank">Telegram</a>
                            </div>
                        </td>
                        <td>
                            <div class="link-box">
                                <a href="https://github.com/MetaCubeX" target="_blank">METACUBEX</a>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
      <footer class="text-center">
    <p><?php echo $footer ?></p>
</footer>
    </div>

    <script src="/www/nekoclash/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
