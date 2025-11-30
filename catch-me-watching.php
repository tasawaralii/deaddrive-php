<?php

if(!isset($_GET['temid'])) {
    header("Location: https://google.com");
    exit;
}

require('db.php');
require('config.php');
require('autoload.php');

$temid = $_GET['temid'];

$file = new File($pdo);

if(strlen($temid) == 5) {
    $file->setUid($temid);
} else {
    if(!$file->getByTemId($temid)) {
        header("Location: https://google.com");
        exit;
    }
}


$user_id = $file->user_id();

if(!$user_id) {
    header("Location: /");
    exit;
}

$user = new User($pdo);
$user->setUserId($user_id);

$userSettings = $user->getuserSettings();

$watchServers = $file->getWatchServers();

if(!isset($_GET['ddpage'])) {
    $file->increaseView();
}

$file->updateLinks();

$downloadButton = ($userSettings['DownloadInPlayer'] && !isset($_GET['ddpage']) ? makeDownloadButtonHTML($file->getDeadDriveDownloadLink()) : "");

function showServerButtons($servers) {
    
    $html = '<ul class="list-server-items">';
    
    foreach($servers as $server) {
        
        $serverLink = "https://{$server['Domain']}{$server['embed']}{$server['slug']}{$server['sufix']}";
        
        $html .= "<li class='linkserver' data-serverId='".$server['server_id']."' data-sandbox='".$server['sandbox']."' data-video=$serverLink>{$server['server_name']}</li>";
    }
    
    $html .=  '</ul>';
    
    return $html;
}

function makeDownloadButtonHTML($link) {
    
    $html = '<div id="dwnldBox"><a id="dwnldPlayer" href="' .$link .'" target="_blank"><img style="width:20px" src="/downloadv2.png"></a></div>';
    return $html;
}
    
?>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=yes">
</head>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<html style="background:black">
<body>
<div class="wrapper">
    <div class="videocontent">
    <?= $downloadButton ?>
    <div id="list-server-more">
      <a href="javascript:void(0)" id="show-server" title="Show Server"></a>
      <?= showServerButtons($watchServers) ?>
    </div>

    <div id="load-iframe">
        <div id="loading">
            <img src="<?= DEADDRIVE_DOMAIN ?>/deaddrive.png" alt="Loading...">
        </div>
        <iframe id="embedvideo" src="" allowfullscreen="true" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0" style="width: 100%;height: 100%;"></iframe>
    </div>
</div>
</div>

<script>
$(document).ready(function () {
    
    closeServer();

    $("#show-server").click(function(e) { 
        e.preventDefault();
        $(".list-server-items").toggle();
    });


    $(".list-server-items li.linkserver").click(function(e) {
        e.preventDefault();
        var link = $(this).attr('data-video');
        var sandbox = $(this).attr('data-sandbox');
        var server_id = $(this).attr('data-serverId');

        if ($(this).hasClass("active")) {
            return false;
        } else {
            
            document.getElementById("embedvideo").style.display = 'none'
            $("#loading").show();

            if (sandbox == 1) {
                $("#embedvideo").attr('sandbox', 'allow-scripts allow-same-origin allow-orientation-lock');
            } else {
                $("#embedvideo").removeAttr('sandbox');
            }

            // $("#load-iframe").show();
            $("#load-iframe iframe").show().attr('src', link);
            
            localStorage.setItem("selectedServer", server_id);
            
            closeServer();   
        }
        

        $('.list-server-items li').removeClass('active');
        $(this).addClass('active');
    });

    
    let lastServer = localStorage.getItem("selectedServer");
    
    if(lastServer) {
        
        $(".list-server-items li.linkserver").each(function () {
        if ($(this).attr("data-serverId") === lastServer) {
            $(this).click();
        }
    });
        
    } else {
        $('.list-server-items li:first-child').click();
    }
    
    
    $("#embedvideo").on("load", function() {
        $("#loading").hide();
        $(this).show();
    });
    
});

function closeServer() {
    setTimeout(function () {
        $('.list-server-items').fadeOut();
    }, 5000);
}


</script>
<script>

    document.querySelectorAll('script[src]').forEach(script => {
    const blockedUrls = [
        "https://earnvids.com/js/ads-ad-bottom-160x600-peel-ads-ad-unit.js?zoneid=8664&ab=1&vast=half-page-ad&wppaszoneid=8111",
        "https://jouwaikekaivep.net/tag.min.js",
        "https://mc.yandex.ru/metrika/tag.js",
        "https://media.daly2024.com/js/code.min.js",
        "https://nikaplayerr.com/player/jw8/vast.js",
        "https://safeframe.googlesyndication.com/safeframe/1-0-40/html",
        "https://www.googletagmanager.com/gtag/js?id=G-HJD8YWWX25"
  ];
  
  if (blockedUrls.some(url => script.src.includes(url))) {
    script.remove();
  }
});

</script>
<style>

       #loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: none;
    }

    #loading img {
        width: 70px;
        height: 70px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .wrapper {
        position: relative;
        background: black;
    }
    .videocontent {
        position: relative;
        color: #000;
    }
    
    #list-server-more {
        /*z-index: 1;*/
        padding: 10px 10px 0 0;
        margin-top: 5px;
        position: absolute;
        color: #fff;
        top: 0;
        right: 8px;
        text-align: right;
        font-family: Arial, Helvetica, sans-serif;
    }
    
    #dwnldBox {
        z-index: 1;
        padding: 10px 10px 0 0;
        margin-top: 5px;
        position: absolute;
        color: #fff;
        top: 0;
        left: 8px;
        text-align: right;
        font-family: Arial, Helvetica, sans-serif;
    }
    
    #show-server {
        color: #fff;
        padding: 5px 15px;
        font-size: 10px;
        background: url('<?= DEADDRIVE_DOMAIN ?>/icon.png') no-repeat center center;
    }
    
    #dwnldPlayer {
        color: #fff;
        padding: 5px 15px;
        font-size: 10px;
        /*background: url('<?= DEADDRIVE_DOMAIN ?>/downloadv2.png') no-repeat center center;*/
    }
    .list-server-items {
        margin-top: 10px;
        background: rgba(0, 0, 0, .7);
    }
    .list-server-items li {
        cursor: pointer;
        padding: 6px 5px 6px 15px;
        color: #ccc;
        text-align: right;
        list-style: none;
        border-top: solid 1px #20201f;
        font-size: 13px;
    }
    .list-server-items li.active, .list-server-items li:hover {
        color: #fff;
        font-weight: bold;
    }
</style>

</body>
</html>