<?php

require('../db.php');

header("Content-type: Application/json");


$uid = $_GET['uid'];

$stmt = $pdo->prepare("SELECT 1 FROM links_info WHERE uid  = :uid AND isStream = 1");

$stmt->execute(['uid' => $uid]);

$isAlreadyStream = $stmt->fetchColumn();

if($isAlreadyStream) {
    echo json_encode(['status' => "success", "message" => "Already in Stream $uid", "already" => true]);
    exit;
}

$pdo->query("UPDATE links_info SET isStream = 1 WHERE uid = '$uid'");
$pdo->query("INSERT INTO animeworld_stream (awi_id,uid) VALUES (null,'$uid')");


echo json_encode(['status' => "success", "message" => "Added in Stream $uid", "already" => false]);
exit;