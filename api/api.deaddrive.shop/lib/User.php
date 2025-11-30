<?php

class User {
    private $user_id = null;
    private $user_api = null;
    private $user_email = null;
    
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
    public function setUserEmail($email) {
        $this->user_email = $email;
    }

    public function getUserId() {
        if(!$this->info) {$this->info();}
        return $this->info['user_id'];
    }

    public static function validateUserApi($apiKey,$db) {
        $stmt = $db->prepare("SELECT * FROM users WHERE api_key = :api_key");
        $stmt->execute([':api_key' => $apiKey]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user) {
            return $user;
        }
        return false;
    }
    
    public function getUserSettings() {
        if(!$this->info) {$this->info();}
        return [
            
            "InstantDownload" => $this->info['instant_download'],
            "DownloadInPlayer" => $this->info['dwnldPlayer'],
            "PlayerInDownloadPage" => $this->info['embedInDwnld']
                
        ];
    }

    public function UserApis($mode = "enabled") {
        
        if($mode == "all") {
            return $this->db->query("SELECT ua.*,si.api_required,si.getApi FROM user_apis ua JOIN server_info si ON si.server_id = ua.server_id WHERE user_id = " . $this->getUserId() . " ORDER BY ua.server_order ASC")->fetchAll();
        } else if($mode == "enabled") {
            return $this->db->query("SELECT * FROM user_apis WHERE enable = 1 AND disTem = 0 AND user_id = " . $this->user_id)->fetchAll();
        }
        
    }

    public function info() {
        
        if($this->info) {return $this->info;}
        
        if($this->user_id != null) {
            $this->info = $this->db->query("SELECT * FROM users WHERE user_id = " . $this->user_id)->fetch();
        } else if($this->user_api != null) {
            $this->info = $this->db->query("SELECT * FROM users WHERE api_key = '" . $this->user_api . "'")->fetch();
        } else if($this->user_email != null) {
            $this->info = $this->db->query("SELECT * FROM users WHERE email = '" . $this->user_email . "'")->fetch();
        }

        if($this->info) {return $this->info;}

        return ["status" => "error", "message" => "Info not found"];

    }
    
    public function userWebsite() {
        
        if(!$this->info) {$this->info();}
        
        if($this->info['site_name'] == '' || $this->info['site_url'] == '') {
            return false;
        }
        return true;
    }
    
    public function setUserWebsite($name,$domain) {
        
        if($domain != "") {
            $domain = (strpos($domain,"https://") !== false) ? $domain : "https://" . $domain;
        }
        
        $stmt = $this->db->prepare("UPDATE users SET site_name = :site_name, site_url = :site_url WHERE user_id = :user_id");
        $stmt->execute([":site_name" => $name, ":site_url" => $domain, ":user_id" => $this->getUserId()]);
        
        $isOldAcc = $this->db->query("SELECT 1 FROM user_apis WHERE user_id = " . $this->getUserId())->fetch();
        
        if(!$isOldAcc) {
            
            $servers = $this->db->query("SELECT * FROM server_info WHERE available")->fetchAll();
            
            $insertIntoApis = $this->db->prepare("INSERT INTO user_apis (user_id,server_id,server_name,server_order) 
                                                    VALUES (:user_id,:server_id,:server_name,:server_order)");
            
            foreach($servers as $s) {
                $insertIntoApis->execute([
                    ':user_id' => $this->getUserId(),
                    ':server_id' => $s['server_id'],
                    'server_name' => $s['Name'],
                    'server_order' => $s['server_order']
                    ]);
            }
            
            $this->db->query("UPDATE user_apis SET enable = 1, api_working = 1 WHERE server_id IN (1,5,6,7,14) AND user_id = " . $this->getUserId());
            
        }
    }
    
    public function setUserPassword($password) {
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        $hpass = $this->db->prepare("UPDATE users SET password = :hpassword WHERE user_id = " . $this->getUserId());
        
        $hpass->execute([':hpassword' => $hashedPassword]);
    
    }
    public function toogleEmbedInDownloadPage() {
        $this->db->query("UPDATE users SET embedIndwnld = !embedIndwnld WHERE user_id = " . $this->getUserId());
    }
    public function toogleDownloadButtonInPlayer() {
        $this->db->query("UPDATE users SET dwnldPlayer = !dwnldPlayer WHERE user_id = " . $this->getUserId());
    }
    
    public function toogleInstantDownloadButton() {
        $this->db->query("UPDATE users SET instant_download = !instant_download WHERE user_id = " . $this->getUserId());
    }
    public function toogleServerTemporarily($server_id) {
        $stmt = $this->db->prepare("UPDATE user_apis SET disTem = !disTem WHERE user_id = :user_id AND server_id = :server_id");
        $stmt->execute([':user_id' => $this->getUserId(),':server_id' => $server_id]);
    }
    public function toogleServerPermanently($server_id) {
        $stmt = $this->db->prepare("UPDATE user_apis SET enable = !enable WHERE user_id = :user_id AND server_id = :server_id");
        $stmt->execute([':user_id' => $this->getUserId(),':server_id' => $server_id]);
    }
    public function getUserServerDetails($server_id) {
        
        $response = [];
        
        $sql = "SELECT ua.server_name, si.Name, si.api_parameters_number,ua.api,ua.email,si.getApi 
                FROM user_apis ua JOIN server_info si ON ua.server_id = si.server_id 
                WHERE ua.user_id = :user_id AND ua.server_id = :server_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $this->getUserId(),':server_id' => $server_id]);
        $res = $stmt->fetch();
        
        if (!$res) {
            return ['status' => "error", 'message' => "Server not found"];
        }
        
        $api_fields = ['api' => $res['api'] ?? ""];
        
        if($res['api_parameters_number'] == 2) {
            $api_fields['email'] = $res['email'] ?? "";
        }
        
       return [
        'status' => "success",
        'server_id' => $server_id,
        'originalName' => $res['Name'],
        'userDefinedName' => $res['server_name'],
        'apiLink' => $res['getApi'],
        'api_fields' => $api_fields
    ];
        
        return $response;

    }
    public function setCustomServerName($server_id,$name) {
        $stmt = $this->db->prepare("UPDATE user_apis SET server_name = :server_name WHERE user_id = :user_id AND server_id = :server_id");
        $stmt->execute(['server_name' => $name, ':user_id' => $this->getUserId(), ':server_id' => $server_id]);
    }
    public function setServerApi($server_id,$api_fields) {
        
        $drive_id = "18cyUzD73CyZsyxNXMzFSe9qkp_OIQ2rX";
        $direct_link = WORKER . '/' . $drive_id . "/" . uniqid() . ".mp4";

        
        if($server_id == 1) {

            $check = Filepress::upload($api_fields['api'],$drive_id);
            
        } elseif($server_id == 2) {

            $check = Playerx::upload($api_fields['api'],$direct_link);
            
        } else if($server_id == 4) {

            $check = Send::upload($api_fields['api'],$direct_link);
            
        } else if($server_id == 5) {

            $check = Filemoon::checkApi($api_fields['api']);
            
        } else if($server_id == 6) {

            $check = StreamHG::checkApi($api_fields['api']);
            
        } else if($server_id == 7) {

            $check = EarnVids::checkApi($api_fields['api']);
            
        } else if($server_id == 9) {

            $check = Voe::checkApi($api_fields['api']);
            
        } else if($server_id == 11) {
        
            $check = Gdtot::upload($drive_id,$api_fields['api'],$api_fields['email']);
            
        } else if($server_id == 14) {

            $check = Hydrax::upload($api_fields['api'],$drive_id);
            
        } else if($server_id == 16) {

            $check = Hubcloud::checkApi($api_fields['api']);
            
        }
        
        if($check['status'] != "success") {
            return $check;
        }
        
        if(!isset($api_fields['email'])) {
            $stmt = $this->db->prepare("UPDATE user_apis SET api = :api WHERE user_id = :user_id AND server_id = :server_id");
            $stmt->execute(['api' => $api_fields['api'], ':user_id' => $this->getUserId(), ':server_id' => $server_id]);
        } else {
            $stmt = $this->db->prepare("UPDATE user_apis SET api = :api, email = :email WHERE user_id = :user_id AND server_id = :server_id");
            $stmt->execute([':api' => $api_fields['api'],':email' => $api_fields['email'], ':user_id' => $this->getUserId(), ':server_id' => $server_id]);
        }
        
        return ['status' => "success"];
    }
    
public function moveServer($server_id, $direction) {
    // Step 1: Get current order of the server
    $sql = "SELECT server_order FROM user_apis WHERE user_id = :user_id AND server_id = :server_id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['user_id' => $this->getUserId(), 'server_id' => $server_id]);
    
    $currentOrder = $stmt->fetchColumn();
    
    if ($currentOrder === false) {
        return ['status' => "error", 'message' => "Server not found"];
    }

    // Step 2: Find the adjacent server based on direction
    $sql = "SELECT server_id, server_order FROM user_apis 
            WHERE user_id = :user_id 
            AND server_order " . ($direction == "up" ? "<" : ">") . " :current_order
            ORDER BY server_order " . ($direction == "up" ? "DESC" : "ASC") . " 
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([':user_id' => $this->getUserId(), ':current_order' => $currentOrder]);
    
    $adjacentServer = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$adjacentServer) {
        return ['status' => "error", 'message' => "Already " . ($direction == "up" ? "first" : "last") . " server"];
    }

    // Step 3: Swap the server orders
    try {
        $this->db->beginTransaction();
        
        // Update the current server
        $sql = "UPDATE user_apis SET server_order = :new_order WHERE server_id = :server_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':new_order' => $adjacentServer['server_order'],
            ':server_id' => $server_id,
            ':user_id' => $this->getUserId()
        ]);

        // Update the adjacent server
        $stmt->execute([
            ':new_order' => $currentOrder,
            ':server_id' => $adjacentServer['server_id'],
            ':user_id' => $this->getUserId()
        ]);
        
        $this->db->commit();
        return ['status' => "success", 'message' => "Swapped"];
    } catch (Exception $e) {
        $this->db->rollBack();
        return ['status' => "error", 'message' => "Failed to swap servers"];
    }
}
}
