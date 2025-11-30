<?php

require('db.php');
require('functions.php');
require('config.php');

if(!isset($_GET['temid'])){header("Location: /");exit;}

$temid = $_GET['temid'];

$res = $pdo->query("SELECT * FROM links_info JOIN users ON users.user_id = links_info.user JOIN templinks ON templinks.uid = links_info.uid WHERE templinks.TempLinkId = '$temid'")->fetch(PDO::FETCH_ASSOC);

if(!$res) {echo "expired";exit;}
    
$site_name = $res['site_name'] ? $res['site_name'] : "DeadDrive";
$site_url = $res['site_url'] ? (strpos($res['site_url'], "https://") !== false  ? $res['site_url'] : 'https://'.$res['site_url']) : "https://t.me/deaddrived";
$page = ['title' => $res['Name']];

$directUrl = WORKER_DOWNLOAD . '/' . $res['Id'] . '/' .$res['Name'];

$directDownloadButton = ($res['live'] != 0 && $res['instant_download']) ? '<br><a class="btn btn-success" style="margin:4px 4px;" href="'. $directUrl .'"><i class="fa fa-arrow-down"></i> Instant Download</a><br>' : "";

$isZip = ($res['Type'] == 'zip' || $res['Type'] == 'rar' );

$link_id = $res['link_id'];

$servers = $pdo->query("SELECT * FROM servers_links 
                        JOIN links_info ON links_info.link_id = servers_links.link_id
                        JOIN server_info ON server_info.server_id = servers_links.server_id 
                        JOIN user_apis ON user_apis.server_id = servers_links.server_id AND user_apis.user_id = links_info.user
                        WHERE servers_links.link_id = $link_id AND server_info.down = 1 AND user_apis.disTem = 0
                        ORDER BY user_apis.server_order ASC")->fetchAll(PDO::FETCH_ASSOC);

require('includes/head.html');
require('includes/header-not-login.html');

?>

<body>
<main style="margin-top: 58px">
    <div class="container pt-4">
        <div class="card bg-dark">
            <div class="card-header text-center py-3">
                <h4><i class="far fa-file-alt"></i> File Information</h4>       
           </div>
            <div class="card-body">
                <div class="mb-4">
                   <ul class="list-group list-group-flush">
                    <li class="list-group-item" style="color: #fff;"><?= $res['Name'] ?></li>
                    <?php $res['duration'] != '' ? '<li class="list-group-item" style="color: #fff;">Duration : '.$res['duration'].'</li>' : '' ?>
                    <?php $res['mimeType'] != '' ? '<li class="list-group-item" style="color: #fff;">MimeType : '.$res['mimeType'].'</li>' : '' ?>
                    <li class="list-group-item" style="color: #fff;">Formate : <?= $res['mimeType'] ?></li>
                    <li class="list-group-item" style="color: #fff;">Size : <?= formatBytes($res['size']) ?></li>
                    <li class="list-group-item" style="color: #fff;">Added on : <?= substr($res['new_date'], 0 ,10) ?></li>
                    <li class="list-group-item" style="color: #fff;">Owner: <?= '<a style="color:#4287f5;" href= "'. $site_url .'">' .$site_name .'</a>' ?></li>
                   </ul>
               </div>
                <div class="text-center">
                    <small class="text-muted">
                        <font color="white">You can try download using any <strong>GDrive Links</strong></font>
                    </small>
                    <div class="center"></div>
                    <?= $directDownloadButton . "<br>" ?>
                    <?php
                    
                    foreach($servers as $s)
                        echo makeDButton($s,$isZip);
                    
                    ?>
                    <br><br>
                    <div class="regenerate-links">
                        <button class="btn btn-danger" onclick="regenerate()"><i class="fa fa-chain-broken" aria-hidden="true"></i> Regenerate Dead Links</button>
                    </div>
                    <br><br>
                    <?php
                    
                    if(!$isZip && $res['embedInDwnld']) {
                        echo embedPlayer($res['uid']);
                    }
                    
                    ?>

                </div>
            </div>
        </div>
    </div>   
</main>




<!--Modal-->

<div class="modal fade" id="fileDetailsModal" tabindex="-1" aria-labelledby="fileDetailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileDetailsLabel">File Details</h5>
                <button type="button" class="btn-close" onclick="location.reload()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 id="fileName"></h6>
                <ul id="serverDetails" class="list-group"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="location.reload()">Close</button>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript" src="<?= DEADDRIVE_DOMAIN ?>/js/mdb.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="<?PHP DEADDRIVE_DOMAIN . '/css/file.css' ?>" rel="stylesheet"/>
<script>

    let urlParams = new URLSearchParams(window.location.search);
    let fileId = urlParams.get("id");

    function regenerate() {
        fetch("<?= DEADDRIVE_DOMAIN ?>/ajax/regenerate-links", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "file=" + fileId
            // body: "file=" + encodeURIComponent(window.location.pathname.split("/")[2])
        })
        .then(response => response.json())
        .then(res => {
            if(res.status == "success") {
                
                var responseData = res;
                
                document.getElementById("fileName").textContent = responseData["0"].Name[0];
                const serverDetails = document.getElementById("serverDetails");
                
                for (const [server, details] of Object.entries(responseData["0"])) {
                    if (server !== "Name" && server !== "Date") {
                        const listItem = document.createElement("li");
                        listItem.className = "list-group-item";
                        listItem.innerHTML = `<strong>${server}:</strong> ${details.join(", ")}`;
                        serverDetails.appendChild(listItem);
                    }
                }
                new bootstrap.Modal(document.getElementById("fileDetailsModal"), {
                    backdrop: "static",
                    keyboard: false
                }).show();
            }
        })
        .catch(error => console.error("Error:", error));
    }
    

</script>
<style>
    @media only screen and (max-width: 479px) {
        .player {
            padding: 0;
            min-height: 260px;
            background-color: #000;
        }
    }
    .player {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        width: 100%;
        background-color: #000;
        border:1px solid white;
    }
    iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>


</body>

<?php

function makeDButton($s,$isZip) {
    
    $button = "";
    
    $button .= '<a ';
    
    $button .= 'class="btn fw-bold btn-' . $s['btnType'] . $s['Color'] . '" ';
    $button .= 'style="margin:3px;" ';
    $button .= 'href="https://' .$s['server_domain'];
    $button .= ($s['server_id'] == 16 && $isZip) ? $s['zipUrl'] : $s['download'];
    $button .= $s['slug'];
    $button .= (($s['server_id'] == 19 || $s['server_id'] == 20) ? "&dl=1" : "");
    $button .= '">';
    $button .= '<i class="' .$s['faIcon'] . '"></i> ';
    $button .= $s['server_name'];
    
    $button .= '</a>';
    
    return $button;
    
}

function embedPlayer($uid) {
    
    $player = "";
    
    $player .= '<div class="player">';
    $player .= '<iframe src="'. DEADDRIVE_DOMAIN . '/embed/' . $uid . '?page=download' .'" allowfullscreen></iframe>';
    $player .= '</div>';
    
    return $player;
}