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
    logMessage("Current version: $current_version");
} else {
    logMessage("Current version file does not exist, assuming not installed.");
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
        logMessage("No suitable download link found for architecture: $current_arch");
        echo "No suitable download link found for architecture: $current_arch";
        exit;
}

logMessage("Latest version: $latest_version");
logMessage("Current architecture: $current_arch");
logMessage("Download link: $download_url");

if (trim($current_version) === trim($latest_version)) {
    logMessage("Current version is already the latest, no update needed.");
    echo "Current version is already the latest.";
    exit;
}

logMessage("Starting core update download...");
exec("wget -O '$temp_file' '$download_url' 2>&1", $output, $return_var);
logMessage("wget output: " . implode("\n", $output));
logMessage("wget return value: $return_var");

if ($return_var === 0) {
    $temp_unzip_file = '/tmp/mihomo-linux-arm64-neko';

    logMessage("Extraction command: gzip -d -c '$temp_file' > '$temp_unzip_file'");
    exec("gzip -d -c '$temp_file' > '$temp_unzip_file' 2>&1", $output, $return_var);
    logMessage("Extraction output: " . implode("\n", $output));
    logMessage("Extraction return value: $return_var");

    if ($return_var === 0) {
        logMessage("Renaming file: mv '$temp_unzip_file' '$install_path'");
        exec("mv '$temp_unzip_file' '$install_path' 2>&1", $output, $return_var);
        logMessage("Renaming output: " . implode("\n", $output));
        logMessage("Renaming return value: $return_var");

        if ($return_var === 0) {
            exec("chmod 0755 '$install_path'", $output, $return_var);
            logMessage("Permission setting command: chmod 0755 '$install_path'");
            logMessage("Permission setting return value: $return_var");

            if ($return_var === 0) {
                logMessage("Core update complete! Current version: $latest_version");
                echo "Update complete! Current version: $latest_version";
            } else {
                logMessage("Failed to set permissions!");
                echo "Failed to set permissions!";
            }
        } else {
            logMessage("Failed to rename file, return value: $return_var");
            echo "Failed to rename file!";
        }
    } else {
        logMessage("Extraction failed, return value: $return_var");
        echo "Extraction failed!";
    }
} else {
    logMessage("Download failed, return value: $return_var");
    echo "Download failed!";
}

if (file_exists($temp_file)) {
    unlink($temp_file);
    logMessage("Cleaning up temporary file: $temp_file");
}
?>
