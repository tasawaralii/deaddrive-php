<?php

require('db.php');

$packs = $pdo->query("SELECT * 
FROM `links_info`
LEFT JOIN `servers_links` ON `servers_links`.`link_id` = `links_info`.`link_id`
                           AND `servers_links`.`server_id` = 4
WHERE `links_info`.`user` = 2 
  AND `links_info`.`Type` = 'zip'
  AND `servers_links`.`server_id` IS NULL
ORDER BY `links_info`.`link_id` DESC LIMIT 1")->fetch();

$link_id = $packs['link_id'];
$api = "246002h0ho8jnt1tat1w3f";
$id = $packs['Id'];
// $reqUrl = "https://hubcloud.club/drive/shareapi.php?key=ekN2aFdQbSs5dGpYN0RPNVhmNUVCdz09&link_add=";
      
//             $res = file_get_contents($reqUrl);
            
//                 if($res !== false) {
//                     $res = json_decode($res,true);
//                         if($res['status'] == 200) {
//                     // print_r($res);
//                     echo "Hubcloud: ";
//                     echo $slug = $res['data'];
//                     $pdo->query("INSERT INTO servers_links (server_id,link_id,slug,api) VALUES ($server_id,$link_id,'$slug','$api')");
//                     $pdo->query("DELETE FROM queue WHERE link_id = $link_id AND server_id = $server_id");
//                         } else {
//                     echo "Hubcloud: ERROR";
//                         }
//                 }

$worker = 'https://snowy-river-337d.bigila1739.workers.dev/' 
           .$id . '/' . str_replace([' ', '\\\'', '\\[', '\\]'], ['+', '', '', ''], $packs['Name']);

echo $apiUrl = "https://send.cm/api/upload/url?key=$api&url=$worker";
exit;
$result = @file_get_contents($apiUrl);

if ($result !== false) {
    
    $response = json_decode($result, true);
    if($response['status'] == 200) {
        
        $slug = $response['result']['filecode'];
        $pdo->query("INSERT IGNORE INTO servers_links (link_id,server_id,slug,api) VALUES ($link_id,4,'$slug','$api')");
    
    }
}

