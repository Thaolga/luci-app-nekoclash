<?php
header('Content-Type: text/plain');

$allowed_files = [
    'plugin_log' => '/etc/neko/tmp/log.txt',
    'mihomo_log' => '/etc/neko/tmp/neko_log.txt',
    'singbox_log' => '/var/log/singbox_log.txt',
];

$file = $_GET['file'] ?? '';

if (array_key_exists($file, $allowed_files)) {
    $file_path = $allowed_files[$file];
    
    if (file_exists($file_path)) {
        echo htmlspecialchars(file_get_contents($file_path));
    } else {
        http_response_code(404);
        echo "File not found.";
    }
} else {
    http_response_code(403);
    echo "Forbidden.";
}
?>