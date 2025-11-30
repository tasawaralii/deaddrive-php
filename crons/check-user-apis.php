<?php
require("../db.php");
require("../config.php");
require("../autoload.php");

$allApis = $pdo->query("SELECT * FROM user_apis WHERE api_working = 0 AND api <> ''")->fetchAll();

$drive_id = "18cyUzD73CyZsyxNXMzFSe9qkp_OIQ2rX";
$direct_link = WORKER . '/' . $drive_id . "/" . uniqid() . ".mp4";

foreach($allApis as $api) {
    
    $isServer = false;
    
    $user_id = $api['user_id'];
    $server_id = $api['server_id'];
    
    if($api['server_id'] == 1) {
        $isServer = true;
        $check = Filepress::upload($api['api'],$drive_id);
        
    } elseif($server_id == 2) {
        $isServer = true;
        $check = Playerx::upload($api['api'],$direct_link);
    } else if($server_id == 4) {
        $isServer = true;
        $check = Send::upload($api['api'],$direct_link);
    } else if($server_id == 5) {
        $isServer = true;
        $check = Filemoon::checkApi($api['api']);
    } else if($server_id == 6) {
        $isServer = true;
        $check = StreamHG::checkApi($api['api']);
    } else if($server_id == 7) {
        $isServer = true;
        $check = EarnVids::checkApi($api['api']);
    } else if($server_id == 9) {
        $isServer = true;
        $check = Voe::checkApi($api['api']);
    } else if($server_id == 14) {
        $isServer = true;
        $check = Hydrax::upload($api['api'],$drive_id);
    } else if($server_id == 16) {
        $isServer = true;
        $check = Hubcloud::checkApi($api['api']);
    }
    
    if($isServer) {
        
        if($check['status'] != "success") {
                echo "Not: ";
                print_r($check);
                echo "Server id " . $api['server_id'] . " " . $api['api'] . " for user " . $api['user_id'] . " is not Working <br>";
                $pdo->query("UPDATE user_apis SET api = '' WHERE user_id = $user_id AND server_id = $server_id");
        } else {
            $pdo->query("UPDATE user_apis SET api_working = 1 WHERE user_id = $user_id AND server_id = $server_id");
        }
    }
    
}