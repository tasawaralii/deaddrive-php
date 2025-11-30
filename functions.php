<?php 


function showAddWebsiteHTML() {
    
    $html = "";
    
    $html .= "<center>";
    
    $html .= '<strong>Please Enter Your Site Name And Url at <i><a href="/account" style="color:royalBlue">Account</a></i> before sharing files.</strong><hr>';
    
    $html .= '<h6>**Instructions**</h6>';
    
    $html .= "</center>";
    
    $html .= "<ol>";
    
    foreach(INSTRUCTIONS as $ins) {
        $html .= '<li>' . $ins . '</li>';
    }
    
    $html .= "</ol>";
    
    return $html;
}


        function template($body,$page,$user,$site) {
            require('includes/head.html');
            echo "<body>";
            require('includes/header.html');
            echo '<main style="margin-top: 58px">
                    <div class="container pt-4">
                        <div class="card">
                            <div class="card-header text-center py-3"> User Servers Setting </div>  
                                <div class="card-body">'
                                .$body
                                .'</div>
                            </div>
                    </div>';
            require('includes/footer.html');
                echo '</main>';
                echo '</body></html>';


        echo '<style>n
    .card { margin-top: 20px}
            </style>';
}

function fetchContent($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        // echo "cURL Error: " . curl_error($ch);
        return false;
    }

    curl_close($ch);
    return $response;
}


function userapis($email, $pdo) {
    $res = $pdo->query("
    SELECT api.*
    FROM api
    JOIN users ON users.user_id = api.user
    WHERE users.email IN ('$email' , 'deaddrived@gmail.com') 
    ORDER BY users.user_id ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    $api = [];
if (count($res) == 1) {
    $api = $res[0];
} else {
    $user_api = $res[1];
    foreach ($user_api as $key => $uapi) {
        $api["$key"] = ($uapi == '' || $uapi == null) ? $res[0]["$key"] : $uapi;
    }
}
    return $api;
}

function files($page_number,$page_size,$email,$pdo) {
    $offset = ($page_number - 1) * $page_size;
}

function checklogin() {
if(!isset($_COOKIE['ddeml'])) {
header("Location: / ");
exit;
        }
}


function userinfo($email, $pdo, $total = false){
    
    $info = $pdo->query("SELECT * FROM users WHERE users.email = '$email'")->fetch(PDO::FETCH_ASSOC);
    
        if($total) {
    $res = $pdo->query("
    SELECT 
    COUNT(*) AS total_files, 
    SUM(size) AS total_size, 
    SUM(downloads) AS downloads, 
    SUM(views) AS views, 
    SUM(CASE WHEN links_info.live = 0 THEN 1 ELSE 0 END) AS broken
FROM 
    links_info 
JOIN 
    users ON users.user_id = links_info.user 
WHERE 
    users.email = '$email';
")->fetch(PDO::FETCH_ASSOC);
    
    $info['total_files'] = $res['total_files'];
    $info['total_size'] = $res['total_size'];
    $info['downloads'] = $res['downloads'];
    $info['views'] = $res['views'];
    $info['broken'] = $res['broken'];
        }
return $info;
    
}

function base_url($slug) {
    echo DEADDRIVE_DOMAIN .'/'.$slug;
}
function mstos($milliseconds) {
    $seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);

    $formattedTime = '';

    if ($hours > 0) {
        $formattedTime .= $hours . 'h ';
    }

    if ($minutes > 0 || $hours === 0) {
        $formattedTime .= ($minutes % 60) . 'min';
    }

    return $formattedTime;
}
function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 
   
    return round($bytes, $precision) ." " . $units[$pow]; 
} 
function AES($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'myencrypt';
    $secret_iv = 'encyptaes';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function fetchFilesFromFolder($folderId, $apiKey) {
    // Corrected indentation for better readability
    $apiUrl = "https://www.googleapis.com/drive/v3/files?q=%27{$folderId}%27+in+parents&fields=files(id,webViewLink,mimeType)&key={$apiKey}&orderBy=name&supportsAllDrives=True&includeItemsFromAllDrives=True";
    $response = fetchContent($apiUrl);
    if ($response === false) {
        return false;
    }
    $data = json_decode($response, true);
    $files = [];
    if (isset($data['files']) && count($data['files']) > 0) {
        foreach ($data['files'] as $file) {
            if ($file['mimeType'] != 'application/vnd.google-apps.folder') {
                // Modified array structure to match the desired format
                $files[] = [
                    'webViewLink' => $file['webViewLink']
                ];
            } else {
                $subfolderFiles = fetchFilesFromFolder($file['id'], $apiKey);
                if ($subfolderFiles !== false) {
                    $files = array_merge($files, $subfolderFiles);
                }
            }
        }
    }
    return $files;
}



function upload($pdo,$id,$email,$googleApiKey,$defaultApis) {
    
    $fetch = 'https://www.googleapis.com/drive/v3/files/' .$id . '?fields=*&supportsAllDrives=True&key=' .$googleApiKey;
    $fileInfo = fetchContent($fetch);
    
    if($fileInfo != false) {
        $f = json_decode($fileInfo,true);
    } else {
        $result = [
        "key" => "error",
        "name" => "Error in Getting File Info",
        "size" => 0,
        "driveId" => $id,
                ];
        return $result;
    }
    
    
    $name = $f['name'];
    $size = $f['size'];
    $extension = $f['fileExtension'];
    $mimeType = $f['mimeType'];
    $duration = isset($f['videoMediaMetadata']) ? $f['videoMediaMetadata']['durationMillis'] : 0;
    $ownerEmail = isset($f['owners'][0]['emailAddress']) ? $f['owners'][0]['emailAddress'] : 'Error';
    
    $user = $pdo->query("SELECT * FROM users WHERE users.email = '$email'")->fetch(PDO::FETCH_ASSOC);
    $user_id = $user['user_id'];
    
    $checkExist = $pdo->query("SELECT Name,uid,size FROM links_info WHERE Id = '$id'")->fetch(PDO::FETCH_ASSOC);
    
    if($checkExist) {
        $result = [
            "key" => $checkExist['uid'],
            "name" => "Already Exist " . $checkExist['Name'],
            "size" => $checkExist['size'],
            "driveId" => $id,
        ];
        
        if($user_id == 2) {
                
            $drive_slug = "https://deadbase.xyz/api/drive-slug?drive="  . $id .  "&size=" . $size . "&slug=" . $checkExist['uid'];
            fetchContent($drive_slug);
        }
        return $result;
            
    }
        
    $worker = 'https://snowy-river-337d.bigila1739.workers.dev/' .$id . '/' . str_replace(' ','+',$name);
    
    $isDownloadable = isFileDownloadable($worker);
    if(!$isDownloadable) {
            $result = [
                "key" => "error",
                "name" => "File is Not Downloadable - " . $name,
                "size" => 0,
                "driveId" => $id,
            ];
        return $result;
        }
    $name = str_replace("'","\'",$name);
    $insert = $pdo->query("INSERT INTO `links_info` (user,Name,live,owner,Id,size,Type,mimeType,duration) VALUES ($user_id,'$name',1,'$ownerEmail','$id',$size,'$extension','$mimeType','$duration')");
    
    // $insert->execute([
    //     ":user" => $user_id,
    //     ":name" => $name,
    //     ":owner" => $ownerEmail,
    //     ":Id" => $id,
    //     ":size" => $size,
    //     ":Type" => $extension,
    //     ":mimeType" => $mimeType,
    //     ":duration" => $duration,
    //     ]);
    
    $afterInsert = $pdo->query("SELECT link_id,uid FROM links_info WHERE Id = '$id'")->fetch(PDO::FETCH_ASSOC);
   
    $zip = ($extension == "zip" || $extension == "rar") ? "AND server_info.supportZip = 1" : "" ;
    $link_id = $afterInsert['link_id'];
    
     $apis = $pdo->query("
     SELECT user_apis.server_id,user_apis.api,server_info.supportZip 
     FROM user_apis 
     JOIN server_info ON server_info.server_id = user_apis.server_id 
     WHERE user_apis.user_id = $user_id AND user_apis.enable = 1 $zip
     ")->fetchAll(PDO::FETCH_ASSOC);
     
        foreach($apis as $a) {
            
            switch ($a['server_id']) {
               
                case 4:
                    // echo "sendcm";
                    
                    if($isDownloadable) {
                    
                sendcm($pdo,$id, $worker, $a['api'] ? $a['api'] : $defaultApis['4'] , $link_id);
                
                    } else {
                        // $pdo->query("INSERT INTO queue ")
                    }
                break;
                default:
                break;
            }
                // break;
        }
    
        if($extension != "zip" && $extension != "rar") {
    $jh = $pdo->prepare("INSERT INTO crons (link_id,user_id,cron_type,drive_id,name) VALUES (:link_id,:user_id,'rest',:drive_id,:name)");
    $jh->execute([':link_id' => $link_id,':user_id' => $user_id, ':drive_id' => $id,':name' => $name]);
        }
    $result = [
                "key" => $afterInsert['uid'],
                "name" => $name,
                "size" => $size,
                "driveId" => $id,
            ];
            
            if($user_id == 2) {
            
            fetchContent("https://deadbase.xyz/api/drive-slug?drive="  . $id .  "&size=" . $size . "&slug=" . $afterInsert['uid']);
            
            }
            
    return $result;
 }


    function vidhide($pdo,$worker,$api,$link_id) {

    $apiUrl = "https://vidhideapi.com/api/upload/url?key=$api&url=$worker";
    $result = @fetchContent($apiUrl);
    if ($result !== false) {
        $response = json_decode($result, true);
        if($response['status'] == 200) {
        $slug = $response['result']['filecode'];
        $pdo->query("INSERT INTO servers_links (server_id,link_id,slug,api) VALUES (7,$link_id,'$slug','$api')");

        }
        if($response['status'] == 400) {
       return;    
    }
} else return; 
        }



function hydrax($pdo,$id, $api,$link_id) {
            $gurl = "https://api.hydrax.net/$api/drive/$id";
            $result = @fetchContent($gurl);
            if ($result !== false) {
                $response = json_decode($result, true);
                $slug = $response['slug'];
    $pdo->query("INSERT IGNORE INTO servers_links (link_id,server_id,slug,api) VALUES ($link_id,14,'$slug','$api')");
    } else {
        return null;
    }
      }


function sendcm($pdo,$id, $worker, $api, $link_id) {
    // $apiUrl = "https://send.cm/api/upload/url?key=$api&url=$worker";
    // $result = @fetchContent($apiUrl);

    // if ($result !== false) {
    //     $response = json_decode($result, true);
    //         if($response['status'] == 200) {
    //     $slug = $response['result']['filecode'];
    //     $pdo->query("INSERT IGNORE INTO servers_links (link_id,server_id,slug,api) VALUES ($link_id,4,'$slug','$api')");
    //         }
    // } else {
    //   return;
    // }
    return;
}

function filepress($pdo, $id, $api, $link_id) {

$domain = 'https://new1.filepress.icu/api/v1/file/add';

$data = array(
    "key" => $api,
    "id" => $id,
    "isAutoUploadToStream" => true
);

$jsonData = json_encode($data);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $domain);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    return;
} else {
    $filepress = json_decode($response, true);
    $slug = $filepress['data']['_id'];
}

curl_close($ch);

$pdo->query("INSERT IGNORE INTO servers_links (link_id,server_id,slug,api) VALUES ($link_id,1,'$slug','$api')");

}

function isFileDownloadable($url) {
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_NOBODY, true);  // Only get the headers
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);  // Return headers
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);  // Set a timeout

    curl_exec($ch);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return ($httpCode == 200);
}

     

function doodstream($id, $api) {

    $gurl = 'https://drive.google.com/file/d/' . $id;
    $apiUrl = "https://doodapi.com/api/upload/url?key=$api&url=$gurl";
    $result = @fetchContent($apiUrl);
    if ($result !== false) {
        // Now $result contains the response from the API
        $response = json_decode($result, true);
        if($response['status'] == 200) {
        $slug = $response['result']['filecode'];
        return $slug;
        }
        if($response['status'] == 400) {
        return null;    
    }
} else return null; 
        }

    
    
      
?>