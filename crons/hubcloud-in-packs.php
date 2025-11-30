<?php
require_once("../db.php");
require_once("../config.php");
require_once("../autoload.php");

if(DEVELOPMENT_MODE) {
    print_r(['status' => 'error', 'message' => "Site is Under Maintainance"]);
    exit;
}


$packs = new Pack($pdo);
$packs->notHubcloud();
