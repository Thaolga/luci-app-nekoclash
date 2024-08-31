<?php
$repo_owner = "Thaolga";
$repo_name = "luci-app-nekoclash"; 
$package_name = "luci-app-nekoclash"; 

$api_url = "https://api.github.com/repos/$repo_owner/$repo_name/releases/latest";
$local_api_response = "/tmp/api_response.json";

$wget_command = "wget --header='User-Agent: PHP' -qO " . escapeshellarg($local_api_response) . " " . escapeshellarg($api_url);
shell_exec($wget_command);

if (!file_exists($local_api_response)) {
    die("无法访问GitHub API。请检查URL或网络连接。");
}

$response = file_get_contents($local_api_response);
$data = json_decode($response, true);
unlink($local_api_response);

$new_version = $data['tag_name'] ?? '';

if (empty($new_version)) {
    die("未找到最新版本或版本信息为空。");
}

$download_url = "https://github.com/Thaolga/luci-app-nekoclash/releases/download/$new_version/{$package_name}_{$new_version}_all.ipk";

echo "<pre>最新版本: $new_version</pre>";

echo "<pre id='logOutput'></pre>";
echo "<script>
        function appendLog(message) {
            document.getElementById('logOutput').innerHTML += message + '\\n';
        }
      </script>";

echo "<script>appendLog('开始下载更新...');</script>"; 

$local_file = "/tmp/$package_name.ipk";
$download_command = "wget -qO " . escapeshellarg($local_file) . " " . escapeshellarg($download_url);
shell_exec($download_command);

if (!file_exists($local_file)) {
    die("下载失败。无法找到下载的文件。");
}

echo "<script>appendLog('下载完成。');</script>"; 

$output = shell_exec("opkg install --force-reinstall " . escapeshellarg($local_file));
echo "<pre>$output</pre>";
echo "<script>appendLog('安装完成。');</script>"; 

unlink($local_file);
?>
