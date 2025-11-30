<?php

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

    public function setLinkId($link_id) { $this->link_id = $link_id; }
    public function setUid($uid) { $this->uid = $uid; }
    
    public function getByTemId($temid) {

        $stmt = $this->db->prepare("SELECT uid FROM templinks WHERE TempLinkId = ?");
        $stmt->execute([$temid]);
        
        $uid = $stmt->fetchColumn();
        
        if($uid) { $this->uid = $uid; return $uid; } 
        else {return false;}
        
    }

    public function isZip() {
        
        if(!$this->info) {$this->info();}
        
        return ($this->info['Type'] == 'zip' || $this->info['Type'] == 'rar');
    }

    public function info() {
        
        if ($this->uid) {
            $this->info = $this->db->query("SELECT * FROM links_info WHERE uid = '" . $this->uid . "'")->fetch();
        } else if($this->link_id) {
            $this->info = $this->db->query("SELECT * FROM links_info WHERE link_id = " . $this->link_id)->fetch();
        }
        
        if ($this->info) {
            $this->user_id = $this->info['user'];
            $this->link_id = $this->info['link_id'];
            $this->uid = $this->info['uid'];
        }
        return $this->info;
    }

    
    public function getLinkId() {
        if(!$this->info) {$this->info();}
        return $this->link_id;
    }
    
    public function getUid() {
        
        if(!$this->info) {$this->info();}
        return $this->uid;
    }


    public function directUrl() {
        if (!$this->info) {
            $this->info();
        }
        return WORKER_DOWNLOAD . "/" . $this->info['Id'] . "/" . urlencode($this->info['Name']);
    }
    
    public function getName() {
        if (!$this->info) {
            $this->info();
        }
        return $this->info['Name'];
    }

    public function getDeadDriveDownloadLink() {
        return DEADDRIVE_DOMAIN . "/file/" . $this->getUid();
    }

    public function isFileDownloadable() {
        return StaticClass::isFileDownloadable($this->directUrl());
    }

    public function setBroken() {
        $this->db->query("UPDATE links_info SET live = 0 WHERE link_id = " . $this->link_id);
    }


    public function driveId() {
        if (!$this->info) {
            $this->info();
        }
        return $this->info['Id'];
    }


    public function user_id() {
        if (!$this->user_id) {
            $this->info();
        }
        return $this->user_id;
    }
    
    private function increase($type) {
        
        $sql = "UPDATE links_info SET ";
        
        $sql .= "$type = $type + 1 ";
        
        $sql .= "WHERE link_id = :link_id";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute(['link_id' => $this->getLinkId()]);
        
    }

    public function increaseView() {
        $this->increase("views");
    }
    public function increaseDownload() {
        $this->increase("downloads");
    }

    public function getServers() {
        
        if($this->servers) {return $this->servers;}
        
        if ($this->link_id) {
            
            $sql = "SELECT sl.slug,sl.server_id,sl.api,
                            si.Domain,si.sufix,si.sandbox,si.download,si.zipUrl,si.embed,si.down,si.watch,si.faIcon,si.btnType,si.Color,
                            ua.server_name,ua.server_id,ua.server_domain,ua.enable,ua.disTem
                    FROM servers_links sl 
                    JOIN server_info si ON si.server_id = sl.server_id
                    JOIN user_apis ua ON ua.server_id = sl.server_id AND ua.user_id = :user_id
                    WHERE link_id = :link_id AND ua.disTem = 0
                    ORDER BY ua.server_order ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $this->user_id(),':link_id' => $this->link_id]);
            
            $this->servers = $stmt->fetchAll();
            
            return $this->servers;
        }
        
        if($this->uid) {
            
            $sql = "SELECT sl.slug,sl.server_id,sl.api,
                            si.Domain,si.sufix,si.sandbox,si.download,si.zipUrl,si.embed,si.down,si.watch,si.faIcon,si.btnType,si.Color,
                            ua.server_name,ua.server_domain,ua.server_id,ua.enable,ua.disTem
                    FROM servers_links sl 
                    JOIN server_info si ON si.server_id = sl.server_id
                    JOIN user_apis ua ON ua.server_id = sl.server_id AND ua.user_id = :user_id
                    JOIN links_info ON links_info.link_id = sl.link_id
                    WHERE links_info.uid = :uid AND ua.disTem = 0
                    ORDER BY ua.server_order ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $this->user_id(),':uid' => $this->uid]);
            $this->servers = $stmt->fetchAll();
            
            return $this->servers;
            
        }

    }
    
    private function filterServers($type) {
        
        if(!$this->servers) {$this->getServers();}
        $result = [];
        
        foreach($this->servers as $server) {
            if(($type == "download" ? $server['down'] : $server['watch'])) {
                $result[] = $server;
            }   
        }
        
        return $result;
    }
    
    public function getDownloadServers() {
        return $this->filterServers("download");
    }
    
    public function getWatchServers() {
        return $this->filterServers("watch");
    }
    
    public function checkServer($server_id) {
        if (!$this->servers) {
            $this->getServers();
        }
        foreach ($this->servers as $server) {
            if ($server['server_id'] == $server_id) {
                return $server;
            }
        }
        return false;
    }


    public function storeServer($server_id, $slug, $api) {
        if ($slug && $this->link_id) {
            $stmt = $this->db->prepare("INSERT INTO servers_links (link_id, server_id, slug, api) VALUES (:link_id, :server_id, :slug, :api)");
            $stmt->execute([
                ":link_id" => $this->link_id, 
                ":server_id" => $server_id, 
                ":slug" => $slug, 
                ":api" => $api
            ]);
            return true;
        }
        return false;
    }
    
    public function updateLinks() {
        
        if(!$this->info){$this->info();}
        
        if (strtotime($this->info['last_updated']) < strtotime('-7 days')) {
                $this->addInQueue();
        }
    }

    public function addInQueue() {
        if(!$this->info){
            $this->info();
        }
        Queue::addinQueue(['fileExtension' => $this->info['Type']],$this->link_id,$this->db);
    }

    public function updateServer($server_id, $slug, $api) {
        if ($slug) {
            $stmt = $this->db->prepare("UPDATE servers_links SET slug = :slug, api = :api WHERE link_id = :link_id AND server_id = :server_id");
            $stmt->execute([
                ":link_id" => $this->link_id, 
                ":server_id" => $server_id, 
                ":slug" => $slug, 
                ":api" => $api
            ]);
        }
    }


    // Add logs
    
    public function storeLogs($logs) {
        $stmt = $this->db->prepare("UPDATE links_info SET logs = :logs WHERE link_id = :link_id");
        $stmt->execute(['logs' => json_encode($logs),"link_id" => $this->link_id]);
    }


    // Delete a Server Entry
    public function deleteServer($server_id) {
        $this->db->query("DELETE FROM servers_links WHERE link_id = " . $this->link_id . " AND server_id = " . $server_id);
        $this->db->query("UPDATE links_info SET live = 0 WHERE link_id = " . $this->link_id);
    }


    public static function addFile($fileInfo, $userId, $db) {
        
        $check = $db->prepare("SELECT link_id,uid FROM links_info WHERE Id = :drive_id");
        
        $check->execute([':drive_id' => $fileInfo['id']]);
        
        $check = $check->fetch();
        
        if($check) {
            
            $stmt = $db->prepare("UPDATE links_info SET Name = :name WHERE Id = :id");
            
            $stmt->execute([
                'name' => $fileInfo['name'],
                'id' => $fileInfo['id']
            ]);
            
            return ['status' => 'AlreadyExist', 'uid' => $check['uid'], 'link_id' => $check['link_id']];
            
        }
        
        $sql = "INSERT INTO links_info (user, Name, live, owner, Id, size, Type, mimeType, duration, logs) 
                VALUES (:user_id, :name, :live, :owner, :id, :size, :type, :mimeType, :duration, :logs)";
                
        $stmt = $db->prepare($sql);
        
        
        $params = [
            ':user_id' => $userId,
            ':name' => $fileInfo['name'],
            ':live' => 1,
            ':owner' => isset($fileInfo['owners'][0]['emailAddress']) ? $fileInfo['owners'][0]['emailAddress'] : $fileInfo['teamDriveId'],
            ':id' => $fileInfo['id'],
            ':size' => $fileInfo['size'] ?? 0,
            ':type' => $fileInfo['fileExtension'] ?? null,
            ':mimeType' => $fileInfo['mimeType'],
            ':duration' => $fileInfo['videoMediaMetadata']['durationMillis'] ?? 0,
            ':logs' => json_encode(['status' => 'File Added in DeadDrive with no Servers'])
        ];
        
        $stmt->execute($params);
        
        $linkId = $db->lastInsertId();
        $uid = $db->query("SELECT uid FROM links_info WHERE link_id = $linkId")->fetchColumn();
        return ['status' => 'NewAdded', 'uid' => $uid, 'link_id' => $linkId];
    }
}
