<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        nav {
            background-color: #007bff;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            margin: 0 15px;
        }
        nav ul li a {
            text-decoration: none;
            color: #ffffff;
            font-size: 16px;
            padding: 10px 20px;
            border-radius: 25px;
            transition: background-color 0.3s, color 0.3s, box-shadow 0.3s;
        }
        nav ul li a:hover {
            background-color: #0056b3;
            color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        nav ul li a.active {
            background-color: #0056b3;
            color: #ffffff;
            font-weight: bold;
        }
        .content {
            padding: 20px;
        }
    </style>
</head>
<body>

<nav>
    <ul>
        <li><a href="?page=upload" class="<?= (!isset($_GET['page']) || $_GET['page'] == 'upload') ? 'active' : '' ?>">Mihomo</a></li>
        <li><a href="?page=upload_sb" class="<?= (isset($_GET['page']) && $_GET['page'] == 'upload_sb') ? 'active' : '' ?>">Sing-box</a></li>
        <li><a href="?page=box" class="<?= (isset($_GET['page']) && $_GET['page'] == 'box') ? 'active' : '' ?>">Conversion Template</a></li>
    </ul>
</nav>

<div class="content">
    <?php
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        
        switch ($page) {
            case 'upload':
                include 'upload.php';
                break;

            case 'upload_sb':
                include 'upload_sb.php';
                break;

            case 'box':
                include 'box.php';
                break;

            default:
                echo "<p>Page Not Found。</p>";
                break;
        }
    } else {
        include 'upload.php';
    }
    ?>
</div>

</body>
</html>
