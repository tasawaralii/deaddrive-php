<?php

require_once("../config.php");
require_once("../db.php");
require_once("../autoload.php");

if(DEVELOPMENT_MODE) {
    print_r(['status' => 'error', 'message' => "Site is Under Maintainance"]);
    exit;
}


$logs = [];
header("Content-Type: Application/json");


for($i = 0; $i < 10; $i++) {
    
    $queue = new Queue($pdo);
    $link_id = $queue->getQueue();
    
    if(!$link_id) {
        echo "Nothing in Queue";
        exit;
    }
    
    $queue->processEnabledServers();
    
    $logs[] = $queue->getlogs();
    
}

print_r($logs);

?>