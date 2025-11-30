<?php
require("../db.php");
require("../config.php");
require_once("../autoload.php");


header("Content-Type: Application/json");

$users = $pdo->query("SELECT DISTINCT user_id FROM user_apis")->fetchAll();
// $users = $pdo->query("SELECT u.user_id FROM users u WHERE u.site_url <> '' AND NOT EXISTS (SELECT 1 FROM user_apis WHERE user_id = u.user_id);")->fetchAll();
// $servers = $pdo->query("SELECT * FROM server_info")->fetchAll();


// foreach ($servers as $s) {
//     $stmt = $pdo->prepare("UPDATE user_apis SET server_domain = :server_domain WHERE server_id = :id");
//     $stmt->execute([':server_domain' => $s['Domain'], 'id' => $s['server_id']]);
// }


$logs = new Log();

foreach ($users as $user) {
    $userHandler = new User($pdo);
    $userHandler->setUserId($user['user_id']);
    $logs->log($user['user_id'],$userHandler->refreshServers());
}

echo json_encode($logs->getLogs());
