<?php
$repo_owner = "Thaolga";
$repo_name = "luci-app-nekoclash"; 
$package_name = "luci-app-nekoclash"; 

$api_url = "https://api.github.com/repos/$repo_owner/$repo_name/releases/latest";
$local_api_response = "/tmp/api_response.json";

$wget_command = "wget --header='User-Agent: PHP' -qO " . escapeshellarg($local_api_response) . " " . escapeshellarg($api_url);
shell_exec($wget_command);

if (!file_exists($local_api_response)) {
    die("Unable to access GitHub API. Please check the URL or network connection.");
}

$response = file_get_contents($local_api_response);
$data = json_decode($response, true);
unlink($local_api_response);

$new_version = $data['tag_name'] ?? '';

if (empty($new_version)) {
    die("Latest version not found or version information is empty.");
}

$download_url = "https://github.com/Thaolga/luci-app-nekoclash/releases/download/$new_version/{$package_name}_{$new_version}_all.ipk";

echo "<pre>Latest version: $new_version</pre>";

echo "<pre id='logOutput'></pre>";
echo "<script>
        function appendLog(message) {
            document.getElementById('logOutput').innerHTML += message + '\\n';
        }
      </script>";

echo "<script>appendLog('Starting update download...');</script>"; 

$local_file = "/tmp/$package_name.ipk";
$download_command = "wget -qO " . escapeshellarg($local_file) . " " . escapeshellarg($download_url);
shell_exec($download_command);

if (!file_exists($local_file)) {
    die("Download failed. Unable to find the downloaded file.");
}

echo "<script>appendLog('Download complete.');</script>"; 

$output = shell_exec("opkg install --force-reinstall " . escapeshellarg($local_file));
echo "<pre>$output</pre>";
echo "<script>appendLog('Installation complete.');</script>"; 

unlink($local_file);
?>
