<?php
require("../db.php");
require("../config.php");
require("../autoload.php");

header("Access-Control-Allow-Origin: ".WORKER);
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// if($_SERVER['REQUEST_METHOD'] != "POST") {
//     header("HTTP/1.1 404 Not Found");
//     exit();
// }

// if(!isset($_POST['file'])) {
//     StaticClass::dieError("Not a Valid Request");
// }

// $temId = $_POST['file'];
$temId = $_GET['file'];

$stmt = $pdo->prepare("SELECT link_id,isStream FROM links_info WHERE links_info.uid = :uid");
$stmt->execute([":uid" => $temId]);
$res = $stmt->fetch();

$link_id = $res['link_id'];

$queue = new Queue($pdo);
$queue->setLinkId($link_id);
$queue->setup();

if($res['isStream']) {
    $queue->processAllServers();
} else {
    $queue->processEnabledServers();
}

StaticClass::dieSuccess([$queue->getLogs()]);