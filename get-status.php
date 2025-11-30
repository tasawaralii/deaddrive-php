<?php
require('db.php');
require("functions.php");

$status = $pdo->query('SELECT * FROM getstatus WHERE server_id = 2 ORDER BY RAND() LIMIT 20')->fetchAll(PDO::FETCH_ASSOC);


    foreach($status as $s) {
        if($s['server_id'] == 8) {
        get_vidguard_slug($pdo,$s['api'],$s['slug_id'],urlencode($s['name']),$s['link_id'],$s['server_id']);
        } else 
        if($s['server_id'] == 2) {
            checkPlayerxStatus($pdo,$s['api'],$s['slug_id'],$s['link_id'],$s['server_id']);
        }
        echo "<br>";
    }

    function checkPlayerxStatus($pdo,$api,$slug,$linkId,$server_id) {
        
                $checkStatus = "https://www.playerx.stream/api.php?slug=".$slug."&api_key=".$api."&action=detail_video";
                $res = fetchContent($checkStatus);
                $res = json_decode($res, true);
                    if($res['result'] == true) {
                        $pdo->query("INSERT IGNORE INTO servers_links (link_id,server_id,slug) VALUES ($linkId,2,'$slug')");
                        $pdo->query("DELETE FROM getstatus WHERE server_id = 2 AND slug_id = '$slug'");
                        echo "Done";
                        echo "<br>";
                    } else {
                        echo "Not Ready Yet";
                        echo "<br>";
                    }
    }


function get_vidguard_slug($pdo, $api, $id, $name, $link_id, $server_id) {
    // Initialize cURL session for the first request
    $ch = curl_init();
    
    // Set cURL options for the first request
    curl_setopt($ch, CURLOPT_URL, "https://api.vidguard.to/v1/remote/get");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
    curl_setopt($ch, CURLOPT_HTTPGET, true); // Use GET method
    
    // Add the parameters
    $queryParams = http_build_query([
        'key' => $api,
        'id' => $id
    ]);
    
    curl_setopt($ch, CURLOPT_URL, "https://api.vidguard.to/v1/remote/get?$queryParams");
    
    $res = curl_exec($ch);
    curl_close($ch); // Close the cURL session
    
    if ($res !== false) {
        $res = json_decode($res, true);
        
        if ($res['msg'] == "Done") {
            $slug = $res['result']['VideoHashID'];
            echo "Vidguard ";
            // Initialize cURL session for the second request
            $chRename = curl_init();
            
            // Set cURL options for the second request
            curl_setopt($chRename, CURLOPT_URL, "https://api.vidguard.to/v1/video/rename");
            curl_setopt($chRename, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
            curl_setopt($chRename, CURLOPT_HTTPGET, true); // Use GET method
            
            // Add the parameters
            $renameParams = http_build_query([
                'key' => $api,
                'id' => $slug,
                'name' => $name
            ]);
            
            curl_setopt($chRename, CURLOPT_URL, "https://api.vidguard.to/v1/video/rename?$renameParams");
            
            $rename = curl_exec($chRename);
            curl_close($chRename); // Close the cURL session
            
            if ($rename !== false) {
                $rename = json_decode($rename, true);
                echo $slug;
                $pdo->query("INSERT INTO servers_links (server_id, link_id, slug, api) VALUES ($server_id, $link_id, '$slug', '$api')");
                $pdo->query("DELETE FROM getstatus WHERE link_id = $link_id AND server_id = $server_id");
            } else {
                $error = curl_error($chRename);
                echo "cURL Error: " . $error . "<br>";
            }
        }
    } else {
        $error = curl_error($ch);
        echo "cURL Error: " . $error . "<br>";
    }
}


?>