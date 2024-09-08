<?php
function logMessage($message) {
    $logFile = '/var/log/mihomo_update.log'; 
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

function writeVersionToFile($version) {
    $versionFile = '/etc/neko/core/mihomo_version.txt';
    $result = file_put_contents($versionFile, $version);
    if ($result === false) {
        logMessage("Unable to write to version file: $versionFile");
        logMessage("Check if the path exists and ensure the PHP process has write permissions.");
    } else {
        logMessage("Successfully wrote to version file: $versionFile");
    }
}

$latest_version = 'v1.18.8'; 
$current_version = ''; 
$install_path = '/etc/neko/core/mihomo'; 
$temp_file = '/tmp/mihomo.gz'; 
$temp_dir = '/tmp/mihomo_temp'; 

if (file_exists($install_path)) {
    $current_version = trim(shell_exec("{$install_path} --version"));
    logMessage("Current version: $current_version");
} else {
    logMessage("Current version file does not exist, assuming not installed.");
}

$current_arch = trim(shell_exec("uname -m"));

$download_url = '';
switch ($current_arch) {
    case 'aarch64':
        $download_url = 'https://github.com/MetaCubeX/mihomo/releases/download/v1.18.8/mihomo-linux-arm64-v1.18.8.gz';
        break;
    case 'armv7l':
        $download_url = 'https://github.com/MetaCubeX/mihomo/releases/download/v1.18.8/mihomo-linux-armv7l-v1.18.8.gz';
        break;
    case 'x86_64':
        $download_url = 'https://github.com/MetaCubeX/mihomo/releases/download/v1.18.8/mihomo-linux-amd64-v1.18.8.gz';
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
    logMessage("The current version is already the latest, no update needed.");
    echo "The current version is already the latest.";
    exit;
}

logMessage("Starting core update download...");
exec("wget -O '$temp_file' '$download_url'", $output, $return_var);
logMessage("wget return value: $return_var");

if ($return_var === 0) {
    if (!is_dir($temp_dir)) {
        logMessage("Creating temporary extraction directory: $temp_dir");
        mkdir($temp_dir, 0755, true);
    } else {
        logMessage("Temporary extraction directory already exists: $temp_dir");
    }

    logMessage("Extraction command: gunzip -f -c '$temp_file' > '$install_path'");
    exec("gunzip -f -c '$temp_file' > '$install_path'", $output, $return_var);
    logMessage("Extraction return value: $return_var");

    if ($return_var === 0) {
        exec("chmod 0755 '$install_path'", $output, $return_var);
        logMessage("Permission setting command: chmod 0755 '$install_path'");
        logMessage("Permission setting return value: $return_var");

        if ($return_var === 0) {
            logMessage("Core update complete! Current version: $latest_version");
            writeVersionToFile($latest_version); 
            echo "Update complete! Current version: $latest_version";
        } else {
            logMessage("Failed to set permissions!");
            echo "Failed to set permissions!";
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
if (is_dir($temp_dir)) {
    exec("rm -r '$temp_dir'");
    logMessage("Cleaning up temporary extraction directory: $temp_dir");
}
?>
