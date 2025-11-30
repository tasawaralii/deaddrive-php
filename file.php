<?php

$uid = $_GET['uid'];

// require('db.php');
// require_once('config.php');

// $checkexist = $pdo->query("SELECT uid FROM links_info WHERE uid = '$uid'")->fetchColumn();
// if(!$checkexist) {
//     $checkexist = $pdo->query("SELECT uid FROM links_info WHERE Id = '$uid'")->fetchColumn();
//         if(!$checkexist) {
//             exit();
//         }
//     }
    
// $uid = $checkexist;
// $pdo->query("UPDATE links_info SET downloads = downloads + 1 WHERE uid = '$uid'");
// $checktemexist = $pdo->query("SELECT TempLinkId FROM templinks WHERE uid = '$uid'")->fetch(PDO::FETCH_ASSOC);
// if($checktemexist) {
//     header("Location: ".WORKER."/?id=".$checktemexist['TempLinkId']);
//     exit();
// }
// $tempLinkId = bin2hex(random_bytes(16));
// $expirationTime = date('Y-m-d H:i:s', strtotime('+4 hours'));
//     $stmt = $pdo->prepare("INSERT  INTO templinks (Uid, TempLinkId, ExpirationTime) VALUES (?, ?, ?)");
//     $stmt->execute([$uid, $tempLinkId, $expirationTime]);
//     header("Location: ".WORKER."/?id=$tempLinkId");

header("Location: https://deaddrive.shop/file/$uid");
exit();
?>
