<?php

require_once("../db.php");
require_once("../config.php");
require_once("../autoload.php");

Header("Content-Type: Application/json");

$link = $pdo->query("SELECT awi.* FROM animeworld_stream awi JOIN links_info li ON li.uid = awi.uid ORDER BY li.new_date DESC")->fetch();

if($link) {
    $queue = new Queue($pdo);
    $queue->setUid($link['uid']);
    echo json_encode($queue->processAllServers());
    $pdo->query("DELETE FROM animeworld_stream WHERE uid = '".$link['uid']."'");
} else {
    echo json_encode(['nothing in queue']);

}



// try {
// } catch(Exception $e) {
//     echo $link['uid'];
//     echo json_encode($queue->getlogs());
// }


