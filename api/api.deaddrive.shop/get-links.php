<?php
require("autoload.php");
require("db.php");
require("config.php");

header("content-type: application/json");

$file = new File($pdo);

$uid = $_GET['uid'];

$file->setUid($uid);

$links = $file->getWatchServers();

$response = [];

foreach($links as $l) {
    $response[] = [
        'server_name' => $l['server_name'],
        'link' => "https://{$l['server_domain']}{$l['embed']}{$l['slug']}{$l['sufix']}"
    ];   
}

print_r(json_encode($response));
exit;