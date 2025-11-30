<?php

if(!defined('NAME')) {
    include_once("../../config.php");
    include_once("../../db.php");
}

// include_once("../../db.php");


// API Response Helper
class ApiResponse {
    public static function error($message) {
        die(json_encode(['status' => 'error', 'message' => $message]));
    }

    public static function success($data) {
        echo json_encode(array_merge(['status' => 'success'], $data));
        exit;
    }
}

// User Authentication
class UserAuthenticator {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function validateApiKey($apiKey) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE api_key = :api_key");
        $stmt->execute([':api_key' => $apiKey]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Google Drive Service
class GoogleDriveService {
    public function fetchFileInfo($driveId, $apiKey) {
        $url = "https://www.googleapis.com/drive/v3/files/{$driveId}?fields=*&supportsAllDrives=True&key={$apiKey}";
        $content = fetchContent($url); // Assuming fetchContent is defined in functions.php
        $data = json_decode($content, true);
        if (isset($data['error'])) {
            ApiResponse::error('Not a valid Google Drive ID');
        }
        return $data;
    }
}

// Database Manager
class DatabaseManager {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addFile($fileInfo, $userId) {
        try {
            $sql = "INSERT INTO links_info (user, Name, live, owner, Id, size, Type, mimeType, duration) 
                    VALUES (:user_id, :name, :live, :owner, :id, :size, :type, :mimeType, :duration)";
            $stmt = $this->pdo->prepare($sql);
            $params = [
                ':user_id' => $userId,
                ':name' => $fileInfo['name'],
                ':live' => 1,
                ':owner' => $fileInfo['owners'][0]['emailAddress'] ?? null,
                ':id' => $fileInfo['id'],
                ':size' => $fileInfo['size'] ?? 0,
                ':type' => $fileInfo['fileExtension'] ?? null,
                ':mimeType' => $fileInfo['mimeType'],
                ':duration' => $fileInfo['videoMediaMetadata']['durationMillis'] ?? 0
            ];
            $stmt->execute($params);
            $linkId = $this->pdo->lastInsertId();
            $uid = $this->pdo->query("SELECT uid FROM links_info WHERE link_id = $linkId")->fetchColumn();
            return ['status' => 'success', 'uid' => $uid, 'link_id' => $linkId];
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $deadInfo = $this->pdo->query("SELECT link_id, uid FROM links_info WHERE Id = '{$fileInfo['id']}'")->fetch(PDO::FETCH_ASSOC);
                return ['status' => 'exists', 'uid' => $deadInfo['uid'], 'link_id' => $deadInfo['link_id']];
            }
            throw $e;
        }
    }

    public function addinQueue($fileInfo,$userID,$link_id) {
        
        if($fileInfo['fileExtension'] != "zip" && $fileInfo['fileExtension'] != "rar") {
            
            $jh = $this->pdo->prepare("INSERT IGNORE INTO queue (link_id) VALUES (:link_id)");
            $jh->execute([':link_id' => $link_id]);
            
        }
    }

    public function getUserFilepressApi($userId) {
        $stmt = $this->pdo->prepare("SELECT api FROM user_apis WHERE server_id = 1 AND user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchColumn() ?: FILEPRESS_DEFAULT_API;
    }

    public function getUserHydraxApi($userId) {
        $stmt = $this->pdo->prepare("SELECT api FROM user_apis WHERE server_id = 14 AND user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchColumn() ?: HYDRAX_DEFAULT_API;
    }

    public function saveServerLinks($linkId, $links) {
        $stmt = $this->pdo->prepare("INSERT INTO servers_links (link_id, server_id, slug, api) VALUES (:link_id, :server_id, :slug, :api)");
        foreach ($links as $link) {
            if($link['slug'] == NULL) {continue;}
            $stmt->execute([
                ':link_id' => $linkId,
                ':server_id' => $link['server_id'],
                ':slug' => $link['slug'],
                ':api' => $link['api']
            ]);
        }
    }
}

class hydrax {
    
    public static function upload($apikey,$drive_id) {
        $url = "https://api.hydrax.net/$apikey/drive/$drive_id";
        $result = @fetchContent($url);
    
        if ($result !== false) {
            $response = json_decode($result, true);
            return $response['slug'];
        }
        return false;
    }
}

// Filepress API Service
class FilepressService {
    public static function upload($apiKey, $driveId) {
        $domain = 'https://' . FILEPRESS_DOMAIN . '/api/v1/file/add';
        $data = json_encode([
            'key' => $apiKey,
            'id' => $driveId,
            'isAutoUploadToStream' => true
        ]);

        $ch = curl_init($domain);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ],
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode != 200) {
            return ['status' => 'error', 'message' => "cURL error: " . curl_error($ch)];
        }

        $result = json_decode($response, true);
        if(isset($result['data']['_id'])) {
            
            return ['status' => 'success', 'slug' => $result['data']['_id']];
        }
        return ['status' => 'error'];
    }
}


class DefaultApis {
    public static function getApi($server_id) {
        echo "Default Api <br>";
        switch($server_id) {
            case 1:
                return FILEPRESS_DEFAULT_API;
                break;
            case 5:
                return FILEMOON_DEFAULT_API;
                break;
            case 7:
                return EARNVIDS_DEFAULT_API;
                break;
            case 14:
                return HYDRAX_DEFAULT_API;
                break;
            default :
                return null;
        }
    }
}

class Queue {
    private $db = null;
    private $queueId = null;
    private $link_id = null;
    private $file = null;
    private $isDownloadAble = false;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getQueue() {
        if(!$this->queueId) {
            $que = $this->db->query("SELECT * FROM queue ORDER BY link_id DESC LIMIT 1")->fetch();
            if($que) {
                $this->queueId = $que['queue_id'];
                $this->link_id = $que['link_id'];
            }
        }
        return $this->link_id;
    }
    
    private function uploadStreamHG ($api) {
        $streamhg = new StreamHG($api);
        
        $isValidApi = $streamhg->validateApi();
        
        if($isValidApi) {
            $isBroken = false;
            $streamhgExists = $this->file->checkServer(6);
            if($streamhgExists['status'] == "true") {
                $streamhg->setSlug($streamhgExists['result']['slug']);
                $isWorking = $streamhg->isWorking($streamhgExists['result']['api']);
                if($isWorking) {
                    echo "StreamHG Working <br>";
                    return;
                } else {
                    echo "StreamHG is Broken <br>";
                    $streamhg->setSlug(null);
                    
                    // if(!$this->fileDownloadAble) {
                    //     $this->file->deleteServer(6); // tasawar
                    // }
                    
                    $isBroken = true;
                }
            }
            $streamhg->setRemoteUrl($this->file->directUrl());
            $slug = $streamhg->upload();
            if($slug) {
                if($isBroken) {
                    $this->file->updateServer(6,$slug,$api);
                    echo "StreamHG Updated <br>";
                } else {
                    $this->file->storeServer(6,$slug,$api);
                    echo "StreamHG Added <br>";
                }
            } else {
                echo "Not Uploaded On StreamHG <br>";
            }
        } else {
            echo "Not a Valid StreamHG Api: $api<br>";
        }
    }
    
    private function uploadHubcloud($api) {
        $hubcloud = new Hubcloud($api);
        if($hubcloud->validateApi()) {
            $hubcloudExists = $this->file->checkServer(16);
            if($hubcloudExists['status'] == 'true') {
                echo "Hubcloud Exists <br>";
                return;
            }
            $slug = $hubcloud->upload($this->file->driveId());
            if($slug) {
                $this->file->storeServer(16,$slug,$api);
                echo "Hubcloud Added <br>";
            }
        }
    }

    private function uploadEarnvids ($api) {
        $earnvids = new Earnvids($api);
        
        $isValidApi = $earnvids->validateApi();
        
        if($isValidApi) {
            $isBroken = false;
            $earnvidsExists = $this->file->checkServer(7);
            if($earnvidsExists['status'] == "true") {
                $earnvids->setSlug($earnvidsExists['result']['slug']);
                $isWorking = $earnvids->isWorking();
                if($isWorking) {
                    echo "EarnVids Working <br>";
                    return;
                } else {
                    echo "EarnVids is Broken <br>";
                    $earnvids->setSlug(null);
                    $isBroken = true;
                }
            }
            $earnvids->setRemoteUrl($this->file->directUrl());
            $slug = $earnvids->upload();
            if($slug) {
                if($isBroken) {
                    $this->file->updateServer(7,$slug,$api);
                    echo "EarnVids Updated <br>";
                } else {
                    $this->file->storeServer(7,$slug,$api);
                    echo "EarnVids Added <br>";
                }
            } else {
                echo "Not Uploaded On EarnVids <br>";
            }
        } else {
            echo "Not a Valid EarnVids Api: $api<br>";
        }
    }
    
    private function uploadFileMoon($api) {
        
        $filemoon = new Filemoon($api);
        
        $checkApi = $filemoon->validateApi();
        
        if($checkApi['status'] == "success") {
            
            $isBroken = false;
            
            $server_exists = $this->file->checkServer(5);
            // if filemoon already present then check working if working continue
            if($server_exists['status'] == "true") {
                // print_r($server_exists);
                $filemoon->setSlug($server_exists['result']['slug']);
                $isWorking = $filemoon->info();
                
                if($isWorking['status'] == "success") {
                    $filemoon->setSlug(null);
                    echo "FileMoon is Working" . "<br>";
                    return;
                } else {
                    echo "Link is Broken ";
                    $filemoon->setSlug(null);
                    $isBroken = true;
                }
            } else {
                echo "FileMoon Not Present ";
            }
            // exit;
            $filemoon->setRemoteUrl($this->file->directUrl());
            $upload = $filemoon->upload();
            if($upload['status'] == "success") {
                echo $slug = $upload['filecode'];
                if($isBroken) {
                    $this->file->updateServer(5,$slug,$api);
                    echo " FileMoon Updated" . "<br>";
                } else {
                    echo " FileMoon Added" . "<br>";
                    $this->file->storeServer(5, $slug, $api);
                    
                }
            } else {
                print_r($upload);
            }
        } else {
            print_r($checkApi);
            }
    
    }
    
    public function processQueue() {
        
        $this->file = new File($this->db);
        $this->file->setLinkId($this->link_id);
        
        $servers = $this->file->getServers(); // uploaded servers of file 
        
        $this->isDownloadAble = $this->file->isFileDownloadable();
        
        // if(!$this->fileDownloadAble) {
        //     echo "<br>File is not Download Able <br>";
        // }
        
        $user_id = $this->file->user_id();
        
        $user = new User($this->db);
        $user->setUserId($user_id);
        
        $user_apis = $user->UserApis(); // enabled APis of user
        
        // print_r($servers);
        // echo "<hr>";
        // print_r($user_apis);
        // exit;
        
        foreach($user_apis as $enabledServer) {
            
            $server_id = $enabledServer['server_id'];
            if($enabledServer['server_id'] == 1 || $enabledServer['server_id'] == 14) {
                continue;
            }
            
            $api = $user->getApi($server_id);
            
            if($server_id == 5) {
                
                $this->uploadFileMoon($api);
                
            } else if($server_id == 6) {
                
                $this->uploadStreamHG($api);
                
            } else if($server_id == 7) {
                
                $this->uploadEarnVids($api);
                
            } else if($server_id == 16) {
                if(!$api) {
                    continue;
                }
                $this->uploadHubcloud($api);
            }
            
        }
        
        $this->removeFromQueue();

    }
    
    public function removeFromQueue() {

        $this->db->query("DELETE FROM queue WHERE queue_id = " . $this->queueId);
        echo "<br>Queue Id " .$this->queueId ." Link " . $this->link_id . " Removed From Queue <hr>";
        $this->link_id = null;
        $this->queueId = null;
    
    }
}


// $queue = new Queue($pdo);
// $link_id = $queue->getQueue();
// $queue->removeFromQueue($link_id);
// exit;

class User {
    private $user_id = null;
    private $user_api = null;
    
    private $db = null;
    private $info = null;
    private $apis = null;
    
    public function __construct($db) {
        $this->db = $db;
    }
    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }
    public function setUserApi($api) {
        $this->user_api = $api;
    }

    public function UserApis() {
        if($this->user_id != null) {
            $this->apis = $this->db->query("SELECT server_id,api FROM user_apis WHERE enable = 1 AND user_id = " . $this->user_id)->fetchAll();
        }
        return $this->apis;
    }
    public function getApi($server_id) {
        // print_r($this->apis);
        foreach($this->apis as $api) {
            // print_r($api);
            // echo "<hr>";

            if($api['server_id'] == $server_id && $api['api']) {
                // echo "yes";
                // exit;
                return $api['api'];
            }
        }
        // echo "No";
        // exit;
        if($server_id == 16) {
            return false;
        }
        return DefaultApis::getApi($server_id);
    }
    public function info($server_id = null) {
        if($this->user_id != null) {
            $this->info = $this->db->query("SELECT * FROM users WHERE user_id = " . $this->user_id)->fetch();
        }
        if($this->info == null && $this->user_api != null) {
            $this->info = $this->db->query("SELECT * FROM users WHERE api_key = " . $this->user_api)->fetch();
        }
        if($this->info != null) {
            return $this->info;
        }
        return ["status" => "error", "message" => "Info not found"];

    }
}
// $user = new User($pdo);
// $user->setUserId(2);

// print_r($user->UserApis());
// exit;


class File {
    private $db = null;
    private $uid = null;
    private $link_id = null;
    private $user_id = null;
    private $info = null;
    private $servers = null;
    
    public function __construct($db) {
        $this->db = $db;
    }
    public function setLinkId($link_id) {
        $this->link_id = $link_id;
    }
    public function setUid($uid) {
        $this->uid = $uid;
    }
    public function info() {
        if($this->uid) {
            $this->info = $this->db->query("SELECT * FROM links_info WHERE uid = '" . $this->uid ."'")->fetch();
        }
        if(!$this->info && $this->link_id) {
            $this->info = $this->db->query("SELECT * FROM links_info WHERE link_id = " . $this->link_id)->fetch();
        }
        if($this->info) {
            $this->user_id = $this->info['user'];
        }
        return $this->info;
    }
    public function directUrl() {
        if(!$this->info) {
            $this->info();
        }
        return WORKER . "/" . $this->info['Id'] . "/" . urlencode($this->info['Name']);
    }
    public function isFileDownloadable() {
        $url = $this->directUrl();
        
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

    public function driveId() {
        if(!$this->info) {
            $this->info();
        }
        return $this->info['Id'];
    }
    public function user_id() {
        if(!$this->user_id) {
            $this->info();
        }
        return $this->user_id;
    }
    public function getServers() {
        if($this->link_id && !$this->servers) {
            $this->servers = $this->db->query("SELECT server_id,slug,api FROM servers_links WHERE link_id = " . $this->link_id . " ORDER BY server_id ASC")->fetchAll();
        }
        return $this->servers;
    }
    public function checkServer($server_id) {
        if(!$this->servers) {
            $this->getServers();
        }
        foreach($this->servers as $server) {
            if($server['server_id'] == $server_id) {
                return ["status" => "true","result" => $server];
            }
        }
            return ["status" => "false"];
    }
    public function storeServer($server_id, $slug, $api) {
        $stmt = $this->db->prepare("INSERT INTO servers_links (link_id,server_id,slug,api) VALUES (:link_id, :server_id, :slug, :api)");
        $stmt->execute([":link_id" => $this->link_id, ":server_id" => $server_id, ":slug" => $slug, ":api" => $api]);
    }
    public function updateServer($server_id, $slug, $api) {
        $stmt = $this->db->prepare("UPDATE servers_links SET slug = :slug, api = :api WHERE link_id = :link_id AND server_id = :server_id");
        $stmt->execute([":link_id" => $this->link_id, ":server_id" => $server_id, ":slug" => $slug, ":api" => $api]);
    }
    
}

// $file = new File($pdo);
// $file->setLinkId(1909);
// // print_r($file->getServers());

// echo $file->isFileDownloadable();
// exit;


class StreamHG {
    private $apiUrl = STREAMHG_API_URL;
    private $api = null;
    private $slug = null;
    private $remoteUrl = null;
    
    public function __construct($api) {
        $this->api = $api;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function setRemoteUrl($remoteUrl) {
        $this->remoteUrl = $remoteUrl;
    }

    public function getSlug() {
        return $this->slug;
    }

    private function fetch($endpoint) {
        $url = $this->apiUrl . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification (optional)
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result === false) {
            return ["status" => "error", "message" => "Request failed"];
        }

        $response = json_decode($result, true);

        if (!$response) {
            return ["status" => "error", "message" => "Invalid API response"];
        }

        return $response;
    }
    
    public function validateApi() {
        $request = "/account/info?key=" . $this->api;
        $response = $this->fetch($request);
        // print_r($response);

        if($response && isset($response['status']) && $response['status'] == 200) {
            return true;
        }
        return false;
    }
    
    public function upload() {
        if($this->remoteUrl) {
            $request = "/upload/url?key=" .$this->api . "&url=" . $this->remoteUrl;
            $response = $this->fetch($request);
            if($response && $response['status'] == 200 && isset($response['result'])) {
                $this->slug = $response['result']['filecode'];
                return $this->slug;
            }
        }
        return false;
    }
    
    public function isWorking() {
        if($this->slug) {
            $request = "/file/info?key=" . $this->api . "&file_code=" . $this->slug;
            $response = $this->fetch($request);
            if($response) {
                if(isset($response['result'][0]['status']))
                // print_r($response);
                return ($response['result'][0]['status'] == 200);
            }
        }
        return false;
    }

}


class fetch {
    
    public static function request($url) {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return response to script instead of browser
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification (optional)
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if($httpCode != 200 || $result === false) {
            return ["status" => "error", "message" => "Request failed"];
        }

        $response = json_decode($result, true);

        if (!$response) {
            return ["status" => "error", "message" => "Invalid API response"];
        }
        return $response;
    }
}

class Hubcloud {
    private $api;
    
    public function __construct($api) {
        $this->api = $api;
    }
    
    public function validateApi() {
        $request = HUBCLOUD_API_URL . "/drive/shareapi.php?key=".$this->api;
        $response = fetch::request($request);
        if($response['status'] == 400) {
            return false;
        }
        return true;
    }
    public function upload($drive_id) {
        $request = HUBCLOUD_API_URL . "/drive/shareapi.php?key=".$this->api."&link_add=$drive_id";
        $response = fetch::request($request);
        if($response['status'] == "200") {
            $slug = $response['data'];
            return $slug;
        }
    }
}

class Earnvids {
    private $apiUrl = EARNVIDS_API_URL;
    private $api = null;
    private $slug = null;
    private $remoteUrl = null;
    
    public function __construct($api) {
        $this->api = $api;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function setRemoteUrl($remoteUrl) {
        $this->remoteUrl = $remoteUrl;
    }

    public function getSlug() {
        return $this->slug;
    }

    private function fetch($endpoint) {
        $url = $this->apiUrl . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification (optional)
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result === false) {
            return ["status" => "error", "message" => "Request failed"];
        }

        $response = json_decode($result, true);

        if (!$response) {
            return ["status" => "error", "message" => "Invalid API response"];
        }

        return $response;
    }
    
    public function validateApi() {
        $request = "/account/info?key=" . $this->api;
        $response = $this->fetch($request);
        // print_r($response);

        if($response && isset($response['status']) && $response['status'] == 200) {
            return true;
        }
        return false;
    }
    
    public function upload() {
        if($this->remoteUrl) {
            $request = "/upload/url?key=" .$this->api . "&url=" . $this->remoteUrl;
            $response = $this->fetch($request);
            if($response && $response['status'] == 200 && isset($response['result'])) {
                $this->slug = $response['result']['filecode'];
                return $this->slug;
            }
        }
        return false;
    }
    
    public function isWorking() {

        if($this->slug) {
            $request = "/file/info?key=" . $this->api . "&file_code=" . $this->slug;
            $response = $this->fetch($request);
            if($response) {
                if(isset($response['result'][0]['status']))
                // print_r($response);
                return ($response['result'][0]['status'] == 200);
            }
        }
        return false;
    }

}

// $earnvids = new Earnvids("28779o7i72p0vr3jx8les");
// if($earnvids->validateApi()) {
    
//     // $earnvids->setRemoteUrl("https://snowy-river-337d.bigila1739.workers.dev/1YlRRVev95xY9qvNEpY73mMs6ERtJUfGm/Turning%20Mecard%20S01E45%20Battle%20in%20the%20Gap%20of%20Dimensions%201080p%20x265%2010bit%20Dual%20Audio%20[DeadToons].mkv");
//     // $slug = $earnvids->upload();
//     $earnvids->setSlug("ar4af3wkg9eb");
//     echo $earnvids->isWorking();
    
// }

class Filemoon {
    
    private $apiUrl = FILEMOON_API_URL;
    private $api = null;
    private $slug = null;
    private $remoteUrl = null;

    public function __construct($api) {
        $this->api = $api;
    }

    public function setSlug($slug) {
        $this->slug = $slug;
    }

    public function setRemoteUrl($remoteUrl) {
        $this->remoteUrl = $remoteUrl;
    }

    public function getSlug() {
        return $this->slug;
    }

    private function fetch($endpoint) {
        $url = $this->apiUrl . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification (optional)
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result === false) {
            return ["status" => "error", "message" => "Request failed"];
        }

        $response = json_decode($result, true);

        if (!$response) {
            return ["status" => "error", "message" => "Invalid API response"];
        }

        return $response;
    }

    public function validateApi() {
        $response = $this->fetch("/account/info?key=" . $this->api);

        if ($response['status'] == 400) {
            return ["status" => "error", "message" => $response['msg']];
        }

        if ($response['status'] == 200) {
            return ["status" => "success", "info" => $response['result']];
        }

        return ["status" => "error", "message" => "Unexpected API response"];
    }

    public function upload() {
        if (!$this->remoteUrl) {
            return ["status" => "error", "message" => "Remote URL is not set"];
        }

        $response = $this->fetch("/remote/add?key=" . $this->api . "&url=" . urlencode($this->remoteUrl));

        if ($response['status'] == 200) {
            $this->slug = $response['result']['filecode'];
            return ["status" => "success", "filecode" => $this->slug];
        }

        return ["status" => "error", "message" => $response['msg'] ?? "Upload failed"];
    }

    public function info() {
        if (!$this->slug) {
            return ["status" => "error", "message" => "Slug is not set"];
        }

        $response = $this->fetch("/file/info?key=" . $this->api . "&file_code=" . $this->slug);

        if (!isset($response['result'][0])) {
            return ["status" => "error", "message" => "Invalid response"];
        }

        return ["status" => "success", "info" => $response['result'][0]];
    }

    public function status() {
        if (!$this->slug) {
            return ["status" => "error", "message" => "Slug is not set"];
        }

        $response = $this->fetch("/remote/status?key=" . $this->api . "&file_code=" . $this->slug);

        if ($response['status'] == 200) {
            if (!empty($response['result']) && isset($response['result'][0]['status'])) {
                if ($response['result'][0]['status'] == "ERROR") {
                    return ["status" => "error", "message" => $response['result'][0]['error']];
                }
            }

            if (empty($response['result'])) {
                $fileInfo = $this->info();
                if ($fileInfo['status'] == "success") {
                    return ["status" => "success", "result" => $fileInfo['info']];
                }
            }

            return $response;
        }

        return ["status" => "error", "message" => "Unexpected API response"];
    }
}