<?php
require("db.php");
require("functions.php");

    $queue = $pdo->query("SELECT * FROM `queue` WHERE user_id <> 689 AND server_id <> 5 AND server_id != 15 AND server_id != 10 AND server_id != 8 AND server_id != 4 AND error != 1 AND queueType = 'upload' ORDER BY `queue`.`link_id` DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    // $queue = $pdo->query("SELECT * FROM `queue` WHERE server_id = 16 AND queueType = 'upload' LIMIT 2")->fetchAll(PDO::FETCH_ASSOC);
    // $queue =  $pdo->query("SELECT *  FROM `queue` WHERE `user_id` = 108 AND `server_id` = 2 ORDER BY `server_id` DESC")->fetchAll(PDO::FETCH_ASSOC);
        foreach($queue as $q) {
            
            
            
            
            $googleApiKey = "AIzaSyAYL0KQop5h9oZTXvMq0v_yUqyDNgFGdOc";
            $server_id = $q['server_id'];
            $user_id = $q['user_id'];
            $link_id = $q['link_id'];
            $drive_id = $q['drive_id'];
            $api = $q['api'];
            $driveUrl = "https://drive.google.com/file/d/".$drive_id."/view";

            if($api == '') {
                $pdo->query("UPDATE queue SET error = 1 WHERE api = ''");
            }
            
            // $name = str_replace(['\'', ' '] , ['', '+'], $q['name']);
            $name = urlencode($q['name']);
            
        
            
            
            $worker = 'https://snowy-river-337d.bigila1739.workers.dev/' .$drive_id . '/' . urlencode($name);
            
            $isDownloadable = isFileDownloadableQuotaCheck($worker);
            
            
            if($isDownloadable != 200) {
                
                    $exist = isFileDownloadableQuotaCheck($driveUrl);
                    
                    if($exist == 404) {
                        
                    echo "DELETED $driveUrl <br>";
                    $pdo->query("UPDATE links_info SET live = 0 WHERE Id = '$drive_id'");
                    $pdo->query("DELETE FROM queue WHERE drive_id = '$drive_id'");
                    continue;
            	} else {
            	    $worker_working = false;
            	}
                
            }
            
            echo $driveUrl . " ";
            
            switch($server_id) {
                case 2:
                    echo $server_id . " - ";
                    playerx($pdo,$worker,$api,$server_id,$link_id);
                break;
                case 5:
                    echo $server_id . " - ";
                    filemoon($pdo,$worker,$api,$server_id,$link_id);
                break;
                case 6:
                    echo $server_id . " - ";
                    streamwish($pdo,$worker,$api,$server_id,$link_id);
                break;
                // case 7:
                // echo "vidhide ";
                //     vidhide($pdo,$worker,$api,$server_id,$link_id);
                // break;
                case 8:
                    echo $server_id . " - ";
                    vidguard($pdo,$drive_id,$api,$server_id,$link_id,$q['name']);
                break;
                case 9:
                    echo $server_id . " - ";
                    voe($pdo,$worker,$api,$server_id,$link_id);
                break;
                case 16:
                    echo $server_id . " - ";
                    hubcloud($pdo,$drive_id,$api,$server_id,$link_id);
                break;
                default:
                    echo $server_id . "<br>";
                break;
                    
            }
            
            echo "<br>";
        }
        
        
        
        
        
        function hubcloud($pdo,$drive_id,$api,$server_id,$link_id) {
            
        $reqUrl = "https://hubcloud.club/drive/shareapi.php?key=$api&link_add=$drive_id";
            $res = fetchContent($reqUrl);
            
                if($res !== false) {
                    $res = json_decode($res,true);
                        if($res['status'] == 200) {
                    // print_r($res);
                    echo "Hubcloud: ";
                    echo $slug = $res['data'];
                    $pdo->query("INSERT INTO servers_links (server_id,link_id,slug,api) VALUES ($server_id,$link_id,'$slug','$api')");
                    $pdo->query("DELETE FROM queue WHERE link_id = $link_id AND server_id = $server_id");
                        } else {
                    echo "Hubcloud: ERROR";
                        }
                }
    }

        
    function vidguard($pdo,$drive_id,$api,$server_id,$link_id,$name) {
    
    
    $data = [
        "key" => $api,
        "url" => "https://drive.google.com/file/d/$drive_id/view?usp=drive_link"
        ];
    
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL , "https://api.vidguard.to/v1/remote/upload");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    
    
    $response = curl_exec($ch);
    
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if($http == 200) {
        
        $response = json_decode($response, true);
        
        if($response['status'] == 200) {
            $slug_id =  $response['result'][0]['id'];
            echo "Vidguard " .$slug_id;
            $hg = $pdo->prepare("INSERT INTO getstatus (link_id,server_id,slug_id,api,name) VALUES ($link_id,$server_id,'$slug_id','$api',:name)");
            $hg->execute([':name' => $name]);
            $pdo->query("DELETE FROM queue WHERE link_id = $link_id AND server_id = $server_id");
        }
        
    } else {
        print_r($response);
        echo $http;
    }
    
    curl_close($ch);
    
    
}


    function voe($pdo,$driveUrl,$api,$server_id,$link_id) {
        
    $apiUrl = "https://voe.sx/api/upload/url?key=$api&url=$driveUrl";

    $result = @fetchContent($apiUrl);

    if ($result !== false) {
        
        $response = json_decode($result, true);
        echo "Voe: - ";
        echo $slug = $response['result']['file_code'];
        
        $pdo->query("INSERT INTO servers_links (server_id,link_id,slug,api) VALUES ($server_id,$link_id,'$slug','$api')");
        $pdo->query("DELETE FROM queue WHERE link_id = $link_id AND server_id = $server_id");
        
    } else {
        echo "Voe: - ERROR <a href='$apiUrl'>Api Req</a>";
    }
}


//     function vidhide($pdo,$worker,$api,$server_id,$link_id) {

//     echo $apiUrl = "https://vidhideapi.com/api/upload/url?key=$api&url=$worker";
//     $result = fetchContent($apiUrl);
// print_r($result);
//     if ($result !== false) {
//         $response = json_decode($result, true);
//         if($response['status'] == 200) {
            
//         echo "Vidhide: - ";
//         echo $slug = $response['result']['filecode'];
//         $pdo->query("INSERT INTO servers_links (server_id,link_id,slug,api) VALUES ($server_id,$link_id,'$slug','$api')");
//         $pdo->query("DELETE FROM queue WHERE link_id = $link_id AND server_id = $server_id");

//         }
//         if($response['status'] == 400) {
//         echo "Vidhide: - ERROR <a href='$apiUrl'>Api Req</a>";    
//     }
// } else echo "ERROR $apiUrl"; 
//         }


    function streamwish($pdo,$worker,$api,$server_id,$link_id) {

    $apiUrl = "https://api.streamwish.com/api/upload/url?key=$api&url=$worker";
    $result = @fetchContent($apiUrl);

    if ($result !== false) {
        $response = json_decode($result, true);
        if($response['status'] == 200) {
        echo "Streamwish: - ";
        echo $slug = $response['result']['filecode'];
        
                $pdo->query("INSERT INTO servers_links (server_id,link_id,slug,api) VALUES ($server_id,$link_id,'$slug','$api')");
                $pdo->query("DELETE FROM queue WHERE link_id = $link_id AND server_id = $server_id");
                }
        if($response['status'] == 400) {
        echo "Streamwish: -  ERROR";    
    }
} else echo "ERROR $apiUrl"; 
        }


    function filemoon($pdo,$worker,$api,$server_id,$link_id) {

            $apiUrl = "https://filemoonapi.com/api/remote/add?key=$api&url=$worker";
            $result = @fetchContent($apiUrl);
                if ($result !== false) {
                    $response = json_decode($result, true);
                        if($response['status'] == 200) {
                            echo "Filemoon: - ";
                            echo $slug = $response['result']['filecode'];
                            
                $pdo->query("INSERT INTO servers_links (server_id,link_id,slug,api) VALUES ($server_id,$link_id,'$slug','$api')");
                $pdo->query("DELETE FROM queue WHERE link_id = $link_id AND server_id = $server_id");
                } elseif($response['status'] == 400) {
                echo "Filemoon: - ERROR $apiUrl";
                }
}
        }


    function playerx($pdo, $url, $api,$server_id, $link_id) {
        $req = fetchContent("https://www.playerx.stream/api.php?api_key=".$api."&url=".$url."&action=add_remote_url");
        $res = json_decode($req, true);
        if($res['result'] == true) {
                $playerxUrl = $res['player'];
                preg_match("/https:\/\/[a-z]+\.[a-z]+\/v\/([a-zA-Z0-9]+)\//", $playerxUrl, $slug);
                echo "Playerx: - ";
                echo $slug = $slug[1];
                $pdo->query("INSERT IGNORE INTO getstatus (server_id,link_id,slug_id,api,name) VALUES ($server_id,$link_id,'$slug','$api','')");
                $pdo->query("DELETE FROM queue WHERE link_id = $link_id AND server_id = $server_id");
        } else {
            echo $server_id . " Playerx: -  Error";
        }
    }


function isFileDownloadableQuotaCheck($url) {
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    curl_exec($ch);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    

    curl_close($ch);

    return ($httpCode);
}



?>