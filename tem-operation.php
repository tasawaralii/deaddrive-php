<?php

header("content-type: application/json");


if(!isset($_GET['drive']) || !isset($_GET['playerx'])) {
    die(json_encode(['status' => 'error', 'message' => "Drive or Playerx Not Set"]));
}

$drive = $_GET['drive'];
$playerx = $_GET['playerx'];

if(!$drive || !$playerx) {
    die(json_encode(['status' => 'error', 'message' => "Drive or Playerx is empty"]));
}

require('db.php');

$link_id = $pdo->query("SELECT link_id FROM links_info WHERE Id = '".$drive."'")->fetchColumn();

if($link_id) {
    $exist = $pdo->query("SELECT slug FROM servers_links WHERE link_id = $link_id AND server_id = 2")->fetchColumn();
    if(!$exist) {
        $stmt = $pdo->prepare("INSERT INTO servers_links (link_id,server_id,slug,api) VALUES (?,?,?,?)");
        $stmt->execute([$link_id,2,$playerx,"xPwmVHqpF63oskrn"]);
        die(json_encode(['status' => "success", 'message' => "Successfully Added in link_id $link_id", "delete" => 0]));
    } else {
        if($exist == $playerx) {
            die(json_encode(['status' => "success" ,"message" => "Already Exists","delete" => 1, 'slug' => $exist]));
        } else {
            $stmt = $pdo->prepare("UPDATE servers_links SET slug = :playerx WHERE link_id = :link_id AND server_id = 2");
            $stmt->execute(['playerx' => $playerx, 'link_id' => $link_id]);
            die(json_encode(['status' => 'error', 'message' => "Other Playerx Slug available $exist Link Updated"]));
        }
    }
} else {
    die(json_encode(['status' => 'error', 'message' => "Link Don't Exist"]));
}