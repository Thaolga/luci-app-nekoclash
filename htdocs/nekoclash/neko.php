<?php
function logMessage($message) {
    $logFile = '/var/log/mihomo_update.log'; 
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

$latest_version = 'neko_v1.18.1'; 
$current_version = ''; 
$install_path = '/etc/neko/core/mihomo'; 
$temp_file = '/tmp/mihomo.gz'; 

if (file_exists($install_path)) {
    $current_version = shell_exec("{$install_path} --version");
    logMessage("当前版本: $current_version");
} else {
    logMessage("当前版本文件不存在，将视为未安装。");
}

$current_arch = shell_exec("uname -m");
$current_arch = trim($current_arch);

$download_url = '';
switch ($current_arch) {
    case 'aarch64':
        $download_url = 'https://github.com/Thaolga/neko/releases/download/core_neko/mihomo-linux-arm64-neko.gz';
        break;
    case 'armv7l':
        $download_url = 'https://github.com/Thaolga/neko/releases/download/core_neko/mihomo-linux-armv7l-neko.gz';
        break;
    case 'x86_64':
        $download_url = 'https://github.com/Thaolga/neko/releases/download/core_neko/mihomo-linux-amd64-neko.gz';
        break;
    default:
        logMessage("未找到适合架构的下载链接: $current_arch");
        echo "未找到适合架构的下载链接: $current_arch";
        exit;
}

logMessage("最新版本: $latest_version");
logMessage("当前架构: $current_arch");
logMessage("下载链接: $download_url");

if (trim($current_version) === trim($latest_version)) {
    logMessage("当前版本已是最新版本，无需更新。");
    echo "当前版本已是最新版本。";
    exit;
}

logMessage("开始下载核心更新...");
exec("wget -O '$temp_file' '$download_url' 2>&1", $output, $return_var);
logMessage("wget 输出: " . implode("\n", $output));
logMessage("wget 返回值: $return_var");

if ($return_var === 0) {
    $temp_unzip_file = '/tmp/mihomo-linux-arm64-neko';

    logMessage("解压命令: gzip -d -c '$temp_file' > '$temp_unzip_file'");
    exec("gzip -d -c '$temp_file' > '$temp_unzip_file' 2>&1", $output, $return_var);
    logMessage("解压输出: " . implode("\n", $output));
    logMessage("解压返回值: $return_var");

    if ($return_var === 0) {
        logMessage("重命名文件: mv '$temp_unzip_file' '$install_path'");
        exec("mv '$temp_unzip_file' '$install_path' 2>&1", $output, $return_var);
        logMessage("重命名输出: " . implode("\n", $output));
        logMessage("重命名返回值: $return_var");

        if ($return_var === 0) {
            exec("chmod 0755 '$install_path'", $output, $return_var);
            logMessage("设置权限命令: chmod 0755 '$install_path'");
            logMessage("设置权限返回值: $return_var");

            if ($return_var === 0) {
                logMessage("核心更新完成！当前版本: $latest_version");
                echo "更新完成！当前版本: $latest_version";
            } else {
                logMessage("设置权限失败！");
                echo "设置权限失败！";
            }
        } else {
            logMessage("重命名文件失败，返回值: $return_var");
            echo "重命名文件失败！";
        }
    } else {
        logMessage("解压失败，返回值: $return_var");
        echo "解压失败！";
    }
} else {
    logMessage("下载失败，返回值: $return_var");
    echo "下载失败！";
}

if (file_exists($temp_file)) {
    unlink($temp_file);
    logMessage("清理临时文件: $temp_file");
}
?>
