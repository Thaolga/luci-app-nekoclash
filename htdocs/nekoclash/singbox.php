<?php
function logMessage($message) {
    $logFile = '/var/log/sing-box_update.log'; 
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

function writeVersionToFile($version) {
    $versionFile = '/etc/neko/core/version.txt';
    $result = file_put_contents($versionFile, $version);
    if ($result === false) {
        logMessage("Unable to write to version file: $versionFile");
        logMessage("Check if the path exists and ensure the PHP process has write permissions.");
    } else {
        logMessage("Successfully wrote to version file: $versionFile");
    }
}

$latest_version = '1.10.0-beta.8'; 
$current_version = ''; 
$install_path = '/usr/bin/sing-box'; 
$temp_file = '/tmp/sing-box.tar.gz'; 
$temp_dir = '/tmp/singbox_temp'; 

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
        $download_url = 'https://github.com/SagerNet/sing-box/releases/download/v1.10.0-beta.8/sing-box-1.10.0-beta.8-linux-arm64.tar.gz';
        break;
    case 'x86_64':
        $download_url = 'https://github.com/SagerNet/sing-box/releases/download/v1.10.0-beta.8/sing-box-1.10.0-beta.8-linux-amd64.tar.gz';
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

    logMessage("Extraction command: tar -xzf '$temp_file' -C '$temp_dir'");
    exec("tar -xzf '$temp_file' -C '$temp_dir'", $output, $return_var);
    logMessage("Extraction return value: $return_var");

    if ($return_var === 0) {
        logMessage("List of extracted files:");
        exec("ls -lR '$temp_dir'", $output);
        logMessage(implode("\n", $output));

        $extracted_file = glob("$temp_dir/sing-box-*/*sing-box")[0] ?? '';
        if ($extracted_file && file_exists($extracted_file)) {
            logMessage("Move file command: cp -f '$extracted_file' '$install_path'");
            exec("cp -f '$extracted_file' '$install_path'", $output, $return_var);
            logMessage("File replacement return value: $return_var");

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
                logMessage("File replacement failed, return value: $return_var");
                echo "File replacement failed!";
            }
        } else {
            logMessage("Extracted file 'sing-box' does not exist.");
            echo "Extracted file 'sing-box' does not exist.";
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
