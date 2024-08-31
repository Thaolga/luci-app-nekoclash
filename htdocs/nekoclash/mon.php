<?php
$singBoxPath = '/usr/bin/sing-box';

function isSingboxRunning() {
    global $singBoxPath;
    $command = "ps w | grep '$singBoxPath' | grep -v grep";
    exec($command, $output);
    return !empty($output);
}

function controlSingbox($action) {
    global $singBoxPath;
    $command = "$singBoxPath $action";
    exec($command);
    return isSingboxRunning() ? 'Running' : 'Stopped';

}

function getSystemMetrics() {
    $cpuLoad = sys_getloadavg();
    $cpuCount = shell_exec("nproc"); 
    $cpuCount = intval(trim($cpuCount));
    
    if ($cpuCount <= 0) {
        $cpuCount = 1;
    }
    
    $cpuLoadPercentage = ($cpuLoad[0] / $cpuCount) * 100;
    $memoryUsage = shell_exec("free -m | awk '/^Mem:/{print $3\"/\"$2\" MB\"}'");
    return [
        'cpuLoad' => number_format($cpuLoadPercentage, 2) . '%',
        'memoryUsage' => trim($memoryUsage),
    ];
}

function getNetworkTraffic() {
    $interface = 'eth0'; 
    $stats = shell_exec("cat /proc/net/dev | grep '$interface' | awk '{print $2\" \"$10}'");
    
    if ($stats) {
        list($received, $transmitted) = explode(' ', trim($stats));
        return [
            'download' => $received,
            'upload' => $transmitted
        ];
    }
    return [
        'download' => 'N/A',
        'upload' => 'N/A'
    ];
}

function formatBytes($bytes) {
    if ($bytes === 'N/A') return $bytes;
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($units) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return number_format($bytes, 2) . ' ' . $units[$i];
}

function getRealTimeNetworkTraffic() {
    $interface = 'eth0'; 
    $statsFile = '/tmp/network_traffic.txt';
    
    $currentStats = shell_exec("cat /proc/net/dev | grep '$interface' | awk '{print $2\" \"$10}'");
    file_put_contents($statsFile, $currentStats);
    
    sleep(1);  
    
    $newStats = shell_exec("cat /proc/net/dev | grep '$interface' | awk '{print $2\" \"$10}'");
    list($receivedOld, $transmittedOld) = explode(' ', trim($currentStats));
    list($receivedNew, $transmittedNew) = explode(' ', trim($newStats));
    
    $downloadSpeed = $receivedNew - $receivedOld;
    $uploadSpeed = $transmittedNew - $transmittedOld;
    
    return [
        'downloadSpeed' => $downloadSpeed,
        'uploadSpeed' => $uploadSpeed
    ];
}

if (isset($_POST['action'])) {
    $singbox_status = controlSingbox($_POST['action']);
}

$metrics = getSystemMetrics();
$networkTraffic = getNetworkTraffic();
$singbox_status = isSingboxRunning() ? 'Running' : 'Stopped';


if (isset($_GET['metrics'])) {
    header('Content-Type: application/json');
    echo json_encode($metrics);
    exit;
}

if (isset($_GET['network'])) {
    header('Content-Type: application/json');
    echo json_encode($networkTraffic);
    exit;
}

if (isset($_GET['real_time_network'])) {
    header('Content-Type: application/json');
    echo json_encode(getRealTimeNetworkTraffic());
    exit;
}

if (isset($_GET['status'])) {
    header('Content-Type: application/json');
    echo json_encode(['singbox_status' => isSingboxRunning() ? 'Running' : 'Stopped']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sing-box Control Panel</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #000;
            color: #fff;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.5);
            text-align: center;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #0f0;
            text-shadow: 0 0 10px rgba(0, 255, 0, 0.7);
        }
        .status {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .status-text {
            font-size: 1.5em;
            font-weight: bold;
        }
        .status-running {
            color: #28a745;
        }
        .status-stopped {
            color: #dc3545;
        }
        .metrics {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .metrics-text {
            font-size: 1.5em;
            font-weight: bold;
        }
        #animation {
            position: absolute;
            top: 200px;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
        }
        .nav-buttons {
            margin-top: 20px;
        }
        .nav-buttons a {
            display: inline-block;
            margin: 5px;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1em;
            color: #fff; 
        }
        .nav-buttons a:hover {
            opacity: 0.8;
        }
        .back-button {
            background: #333; 
        }
        .current-menu-button {
            background: #007bff; 
        }
        .config-menu-button {
            background: #28a745; 
        }
        .monitoring-button {
            background: #ffc107; 
        }
        .main-menu-button {
            background: #dc3545; 
        }
        .real-time-speed {
            position: fixed;
            top: 20px;
            left: 20px;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
            font-size: 1.5em;
            font-weight: bold;
            z-index: 10;
            perspective: 1000px; 
        }
        .real-time-speed div {
            transform-style: preserve-3d;
            transform: rotateX(10deg) rotateY(15deg);
        }
        .real-time-speed span {
            display: inline-block;
            transform: rotateY(10deg);
            transform: perspective(500px) rotateX(20deg);
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.6);
            border: 1px solid #0056b3;
        }
        .low-speed {
            background: #00d4ff; 
        }
        .medium-speed {
            background: #00bfff; 
        }
        .high-speed {
            background: #1e90ff; 
        }
        .very-high-speed {
            background: #0000ff; 
        }
    </style>
</head>
<body>
    <div id="animation"></div>
    <div class="container">
        <h1>Sing-box Monitoring Panel</h1>
        <div class="status">
            Current Status: <span class="status-text <?php echo $singbox_status === 'Running' ? 'status-running' : 'status-stopped'; ?>"><?php echo $singbox_status; ?></span>
        </div>
        <div class="metrics">
            <div>CPU Load: <span class="metrics-text" id="cpuLoad"><?php echo $metrics['cpuLoad']; ?></span></div>
            <div>Memory Usage: <span class="metrics-text" id="memoryUsage"><?php echo $metrics['memoryUsage']; ?></span></div>
            <div>Download Traffic: <span class="metrics-text" id="downloadTraffic"><?php echo formatBytes($networkTraffic['download']); ?></span></div>
            <div>Upload Traffic: <span class="metrics-text" id="uploadTraffic"><?php echo formatBytes($networkTraffic['upload']); ?></span></div>
        </div>
        <div class="nav-buttons">
            <a href="javascript:history.back()" class="current-menu-button">Go Back to Previous Menu</a>
            <a href="/nekoclash/configs.php" class="config-menu-button">Return to Configuration Menu</a>
            <a href="/nekoclash/upload_sb.php" class="monitoring-button">Sing-box Management Panel</a>
            <a href="/nekoclash" class="main-menu-button">Return to Main Menu</a>
        </div>
    </div>

    <div class="real-time-speed">
        <div>
            <span style="color: blue; background-color: lightyellow;">Real-time Download Speed:</span><span id="downloadSpeed">0 B/s</span><br> 
            <span style="color: orange; background-color: lightblue;">Real-time Upload Speed:</span><span id="uploadSpeed">0 B/s</span> 
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.getElementById('animation').appendChild(renderer.domElement);

        const geometry = new THREE.SphereGeometry(15, 32, 32);
        const material = new THREE.MeshBasicMaterial({ color: 0xffffff, wireframe: true });
        const sphere = new THREE.Mesh(geometry, material);
        scene.add(sphere);

        camera.position.z = 50;

        function animate() {
            requestAnimationFrame(animate);
            sphere.rotation.x += 0.01;
            sphere.rotation.y += 0.01;
            renderer.render(scene, camera);
        }

        animate();
        window.addEventListener('resize', () => {
            renderer.setSize(window.innerWidth, window.innerHeight);
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
        });

        function fetchMetrics() {
            fetch(window.location.href + '?metrics')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cpuLoad').textContent = data.cpuLoad;
                    document.getElementById('memoryUsage').textContent = data.memoryUsage;
                });
        }

        function fetchNetworkTraffic() {
            fetch(window.location.href + '?network')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('downloadTraffic').textContent = formatBytes(data.download);
                    document.getElementById('uploadTraffic').textContent = formatBytes(data.upload);
                });
        }

        function fetchRealTimeNetworkTraffic() {
            fetch(window.location.href + '?real_time_network')
                .then(response => response.json())
                .then(data => {
                    updateSpeedDisplay(data.downloadSpeed, data.uploadSpeed);
                });
        }

        function updateSpeedDisplay(downloadSpeed, uploadSpeed) {
            document.getElementById('downloadSpeed').textContent = formatBytes(downloadSpeed) + '/s';
            document.getElementById('uploadSpeed').textContent = formatBytes(uploadSpeed) + '/s';

            document.getElementById('downloadSpeed').className = getSpeedClass(downloadSpeed);
            document.getElementById('uploadSpeed').className = getSpeedClass(uploadSpeed);
        }

        function getSpeedClass(speed) {
            if (speed < 1024) return 'low-speed'; 
            if (speed < 1024 * 10) return 'medium-speed'; 
            if (speed < 1024 * 100) return 'high-speed'; 
            return 'very-high-speed'; 
        }

        function formatBytes(bytes) {
            if (bytes === 'N/A') return bytes;
            const units = ['B', 'KB', 'MB', 'GB', 'TB'];
            let i = 0;
            while (bytes >= 1024 && i < units.length - 1) {
                bytes /= 1024;
                i++;
            }
            return bytes.toFixed(2) + ' ' + units[i];
        }

        function updateStatus() {
            fetch(window.location.href + '?status')
                .then(response => response.json())
                .then(data => {
                    const statusText = document.querySelector('.status-text');
                    statusText.textContent = data.singbox_status;
                    statusText.className = 'status-text ' + (data.singbox_status === 'Running' ? 'status-running' : 'status-stopped');
                });
        }

        setInterval(fetchMetrics, 5000);
        setInterval(fetchNetworkTraffic, 5000);
        setInterval(fetchRealTimeNetworkTraffic, 1000);  
        setInterval(updateStatus, 5000);

        updateMetrics(); 
        updateNetworkTraffic();
        updateRealTimeNetworkTraffic();
        updateStatus();
    </script>
</body>
</html>
