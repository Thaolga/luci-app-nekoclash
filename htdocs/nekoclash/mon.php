<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            width: 100%;
            height: 100%;
        }
        body {
            font-family: Arial, sans-serif;
            box-sizing: border-box;
        }
        nav {
            background-color: #007bff;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            box-sizing: border-box;
        }
        nav ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            position: relative;
        }
        nav ul li {
            margin: 0 15px;
            position: relative;
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
        .submenu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #007bff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            padding: 10px 0;
        }
        .submenu li {
            margin: 0;
            padding: 0;
        }
        .submenu li a {
            font-size: 14px;
            padding: 10px 20px;
            width: 100%;
            display: block;
            white-space: nowrap;
        }
        .submenu li a:hover {
            background-color: #0056b3;
        }
        nav ul li:hover .submenu {
            display: block;
        }
        .content {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .cbi-map {
            width: 100vw;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: black;
            position: relative;
        }
        .cbi-map iframe {
            width: 100%;
            height: 100%;
            border: none;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<nav>
    <ul>
        <li><a href="?page=upload" class="<?= (!isset($_GET['page']) || $_GET['page'] == 'upload') ? 'active' : '' ?>">Mihomo</a></li>
        <li><a href="?page=upload_sb" class="<?= (isset($_GET['page']) && $_GET['page'] == 'upload_sb') ? 'active' : '' ?>">Sing-box</a></li>
        <li>
            <a href="?page=box" class="<?= (isset($_GET['page']) && ($_GET['page'] == 'box' || $_GET['page'] == 'personal')) ? 'active' : '' ?>">Template</a>
            <ul class="submenu">
                <li><a href="?page=box" class="<?= (isset($_GET['page']) && $_GET['page'] == 'box') ? 'active' : '' ?>">Box</a></li>
                <li><a href="?page=personal" class="<?= (isset($_GET['page']) && $_GET['page'] == 'personal') ? 'active' : '' ?>">Personal</a></li>
            </ul>
        </li>
        <li>
            <a href="?page=neko_yacd" class="<?= (isset($_GET['page']) && ($_GET['page'] == 'neko_yacd' || $_GET['page'] == 'neko_meta')) ? 'active' : '' ?>">Neko Yacd</a>
            <ul class="submenu">
                <li><a href="?page=neko_yacd" class="<?= (isset($_GET['page']) && $_GET['page'] == 'neko_yacd') ? 'active' : '' ?>">Meta-Yacd</a></li>
                <li><a href="?page=neko_meta" class="<?= (isset($_GET['page']) && $_GET['page'] == 'neko_meta') ? 'active' : '' ?>">MetaCubeXD</a></li>
            </ul>
        </li>
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

            case 'personal':
                include 'personal.php';
                break;

            case 'neko_yacd':
                echo '<div class="cbi-map">
                        <iframe id="neko"></iframe>
                      </div>
                      <script type="text/javascript">
                          fetch("/nekoclash/lib/log.php?data=url_dash")
                              .then(response => response.json())
                              .then(data => {
                                  document.getElementById("neko").src = data.yacd;
                              })
                              .catch(error => {
                                  console.error("Error fetching URL data:", error);
                              });
                      </script>';
                break;

            case 'neko_meta':
                echo '<div class="cbi-map">
                        <iframe id="neko"></iframe>
                      </div>
                      <script type="text/javascript">
                          fetch("/nekoclash/lib/log.php?data=url_dash")
                              .then(response => response.json())
                              .then(data => {
                                  document.getElementById("neko").src = data.meta;
                              })
                              .catch(error => {
                                  console.error("Error fetching URL data:", error);
                              });
                      </script>';
                break;

            default:
                include 'upload.php';
                break;
        }
    } else {
        include 'upload.php';
    }
    ?>
</div>

</body>
</html>
