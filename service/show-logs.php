<?php

if ($_SERVER['REQUEST_METHOD'] != "POST") {
    header("HTTP/1.1 404 Not Found");
    exit();
}

if (!isset($_POST['uid'])) {
    StaticClass::dieError("Not a Valid Request");
}

$uid = $_POST['uid'];

$stmt = $pdo->prepare("SELECT logs FROM links_info WHERE uid = :uid");
$stmt->execute([":uid" => $uid]);
$res = $stmt->fetch();

StaticClass::dieSuccess($res);