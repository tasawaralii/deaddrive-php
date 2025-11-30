<?php

require_once("../config.php");
require_once("../db.php");
require_once("../autoload.php");

$logs = new Log;
$slugs = [];

$insertServerstmt = $pdo->prepare("INSERT IGNORE INTO servers_links (link_id,server_id,slug,api) VALUES (:link_id,:server_id,:slug,:api)");

$stmt2 = $pdo->prepare("DELETE FROM getstatus WHERE link_id = :link_id AND server_id = :server_id");

$stepstmt = $pdo->prepare("UPDATE getstatus SET check_step = check_step + 1 WHERE getStatusId = :statusId");

$slugs = $pdo->query("SELECT * FROM getstatus ORDER BY getStatusId DESC LIMIT 200")->fetchAll();

if (!$slugs) {
    $logs->log("Status","No link in Get Queue");
} else {
    $file = new File($pdo);
}

$i = 0;


foreach ($slugs as $slug) {
    
    $i++;
    
    if($slug['server_id'] == 2) {
        
        $response = Playerx::checkStatus($slug['slug_id'], $slug['api']);
        
        if ($response['status'] == "success") {

            try {
                $insertServerstmt->execute(["link_id" => $slug['link_id'],"server_id" => $slug['server_id'], "slug" => $slug['slug_id'], "api" => $slug['api']]);
            } catch(Exception $e) {
                if($e->getcode() == 23000) {
                    $logs->log($slug['link_id'],"Playerx Already Available in File");
                    $stmt2->execute(['link_id' => $slug['link_id'], 'server_id' => $slug['server_id']]);
                    continue;
                }
            }
            
            
            $stmt2->execute(['link_id' => $slug['link_id'], 'server_id' => $slug['server_id']]);
            $logs->log($slug['link_id'], "Playerx Added to file " . $slug['link_id'] . " " . $response['message']);
            
        } else {
            $logs->log($slug['link_id'], $response['message']);
        }
        
    } else if($slug['server_id'] == 19) {
        $upn = new UpnShare(UPNSHARE_API_URL,$slug['api']);
        if($slug['check_step'] == 0) {
            $checkUpload = $upn->getUploadStatus($slug['slug_id']);
            if($checkUpload['status'] == "success") {
                $pdo->query("UPDATE getstatus SET slug_id = '" .$checkUpload['slug'] ."' WHERE getStatusId = ".$slug['getStatusId']);
                $stepstmt->execute(['statusId' => $slug['getStatusId']]);
            } else {
                if(isset($checkUpload['response']['message']) && $checkUpload['response']['message'] == "Upload task not found") {
                    $logs->log("upn", "Upload Not Completed in ".$slug['link_id']. " ". json_encode($checkUpload['response']));
                    $stmt2->execute(['link_id' => $slug['link_id'], 'server_id' => $slug['server_id']]);
                    $logs->log("upn", "Removed From Queue ".$slug['link_id']);

                } else {
                    if(isset($checkUpload['response']['error']) && $checkUpload['response']['error'] == "No URI available.") {
                        $stmt2->execute(['link_id' => $slug['link_id'], 'server_id' => $slug['server_id']]);
                    }
                }
                $logs->log('upn', "unknown: ".$slug['link_id'].json_encode($checkUpload));

            }
        } else if($slug['check_step'] == 1) {
            $playable = $upn->getPlayStatus($slug['slug_id']);
            if($playable['status'] == "success") {
                try{
                    $insertServerstmt->execute(['link_id' => $slug['link_id'], 'server_id' => $slug['server_id'], 'slug' => "#".$slug['slug_id'], 'api' => $slug['api']]);
                    
                    $stmt2->execute(['link_id' => $slug['link_id'], 'server_id' => $slug['server_id']]);
                    $logs->log("upn","Upn Added in ".$slug['link_id']);
                }
                catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            } else {
                $logs->log("upn", "Video Not Ready Yet ". $slug['link_id']);
            }
        }
    } else if($slug['server_id'] == 20) {
        $rpm = new RpmShare(RPMSHARE_API_URL,$slug['api']);
        if($slug['check_step'] == 0) {
            $checkUpload = $rpm->getUploadStatus($slug['slug_id']);
            if($checkUpload['status'] == "success") {
                $pdo->query("UPDATE getstatus SET slug_id = '" .$checkUpload['slug'] ."' WHERE getStatusId = ".$slug['getStatusId']);
                $stepstmt->execute(['statusId' => $slug['getStatusId']]);
            } else {
                $logs->log("Rpm", "Upload Not Completed in ".$slug['link_id'] . " " . json_encode($checkUpload['response']['error']));
                if(($checkUpload['response']['error']) == "Authorization failed.") {
                    $stmt2->execute(['link_id' => $slug['link_id'], 'server_id' => $slug['server_id']]);

                }
            }
        } else if($slug['check_step'] == 1) {
            $playable = $rpm->getPlayStatus($slug['slug_id']);
            if($playable['status'] == "success") {
                $insertServerstmt->execute(['link_id' => $slug['link_id'], 'server_id' => $slug['server_id'], 'slug' => "#".$slug['slug_id'], 'api' => $slug['api']]);
                $stmt2->execute(['link_id' => $slug['link_id'], 'server_id' => $slug['server_id']]);
                $logs->log("Rpm","Rpm Added in ".$slug['link_id']);
            } else {
                $logs->log("Rpm", "Video Not Ready Yet ". $slug['link_id']);
            }
        }
    }
}

$logs->log("Counter",$i);

header("Content-type: Application/json");
echo json_encode($logs->getlogs());
