<?php

class Queue {
    private $db = null;
    private $queueId = null;
    private $link_id = null;
    
    private $file = null;
    
    private $user = null;
    
    private $isDownloadAble = false;
    private $logs = null;

    public function __construct($db) {
        $this->db = $db;
    }
    
    public function setLinkId($link_id) {
        if($this->link_id) {
            return false;
        }
        $this->link_id = $link_id;
        return true;
    }
    
    public function setup() {
        
        if(!$this->link_id) { return ['status' => "error", 'message' => "No LinkId Available"];}
        
        $this->logs = new Log();

        $this->file = new File($this->db);
        $this->file->setLinkId($this->link_id);
        
        $this->logs->log("Name", $this->file->getName());
                
        $this->isDownloadAble = $this->file->isFileDownloadable();
        
        if(!$this->isDownloadAble) { $this->logs->log("File Status", "File is not Downloadable");}
        
        
        $user_id = $this->file->user_id();
        
        $this->user = new User($this->db);
        $this->user->setUserId($user_id);
        
    }
    public function getlogs() {return $this->logs->getlogs();}
    public function processQueue() {

        $this->setup();
        
        $user_apis = $this->user->UserApis();

        foreach($user_apis as $enabledServer) {
            
            $server_id = $enabledServer['server_id'];
            
            $api = $enabledServer['api'];

            if(!$api || $api == "") {
                $api = StaticClass::DefaultApis($server_id);
            }
            
            if(!$api) {
                continue;
            }

            if($server_id == 1) {
                
                $this->uploadFilepress($api);
                
            } else if($server_id == 2) {
                
                $this->uploadPlayerx($api);
                
            } else if($server_id == 4) {
                
                $this->uploadSend($api);
                
            } else if($server_id == 5) {
                
                $this->uploadFileMoon($api);
                
            } else if($server_id == 6) {
                
                $this->uploadStreamHG($api);
                
            } else if($server_id == 7) {
                
                $this->uploadEarnVids($api);
                
            } else if($server_id == 9) {
                
                $this->uploadVoe($api);
                
            } else if($server_id == 11) {
                
                $this->uploadGdtot();
                
            } else if($server_id == 14) {
                
                $this->uploadHydrax($api);
                
            } else if($server_id == 16) {
                if(!$api) {
                    $this->logs->log("Hubcloud", "Api not Available");
                    continue;
                }
                $this->uploadHubcloud($api);
            }
            
        }
        
        $this->file->storeLogs($this->getlogs());
        $this->removeFromQueue();
        
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
    public static function addinQueue($fileInfo,$link_id,$db) {
        
        if($fileInfo['fileExtension'] != "zip" && $fileInfo['fileExtension'] != "rar") {
            
            $jh = $db->prepare("INSERT IGNORE INTO queue (link_id) VALUES (:link_id)");
            $jh->execute([':link_id' => $link_id]);
            
        }
    }
    public function addinGetStatus($server_id, $slug, $api) {
        
        $stmt = $this->db->prepare("INSERT IGNORE INTO getstatus (link_id, server_id, slug_id, api) VALUES (:link_id, :server_id, :slug, :api)");
        $stmt->execute([
            'link_id' => $this->link_id,
            'server_id' => $server_id,
            'slug' => $slug,
            'api' => $api
        ]);
    }
    public function isInGetStatus($server_id) {
        
        $stmt = $this->db->prepare("SELECT 1 FROM getstatus WHERE link_id = :link_id AND server_id = :server_id");
        $stmt->execute([":link_id" => $this->link_id, ":server_id" => $server_id]);
        return (bool) $stmt->fetchColumn();
    }

    public function removeFromQueue() {

        if($this->link_id) {
            
            $this->db->query("DELETE FROM queue WHERE link_id = " . $this->link_id);
            $this->db->query("UPDATE links_info SET last_updated = NOW() WHERE link_id = " . $this->link_id);
            
            $this->link_id = null;
            $this->queueId = null;
        
        }
            
    }
    
    
    // Helpers
    
    public function uploadSend($api) {
        
        $sendExists = $this->file->checkServer(4);
        
        if($sendExists) {
        
            $this->logs->log("Send","Send is present in file");
            
            $isWorking = Send::isWorking($sendExists['api'],$sendExists['slug']);

            if($isWorking['status'] == "success") {$this->logs->log("Send","Send is Working");return;} 
            else {
    
                $this->logs->log("Send","Send is Broken");
                
                $this->file->deleteServer(4);
                $this->logs->log("Send","Send is Deleted From file");

            }
        }

        if($this->isDownloadAble) {
            $slug = Send::upload($api,$this->file->directUrl());
            if($slug['status'] == "success") {
                $this->file->storeServer(4,$slug['message'],$api);
                $this->logs->log("Send","Send Added in file " . $slug['message']);
            } else {
                $this->logs->log("Send","Send is not Uploaded " . $slug['message']);
            }
        }
    }
    
    public function uploadFilepress($api) {
        
        if(!$this->file->checkServer(1)) {
            
            $slug = Filepress::upload($api,$this->file->driveId());
            if($slug['status'] == "success") {
            $this->file->storeServer(1,$slug['message'],$api);
            $this->logs->log("Filepress", "Added in file");
            return;

            }
        }
        $this->logs->log("Filepress", "Already Exists in file");
    }
    
    
    public function uploadGdtot() {
        
        if(!$this->file->checkServer(11)) {
    
            $slug = Gdtot::upload($this->file->driveId(),GDTOT_DEFAULT_API['api'],GDTOT_DEFAULT_API['gmail']);
            if($slug['status'] == "success") {
                
                $this->file->storeServer(11,$slug['message'],GDTOT_DEFAULT_API['api']);
                $this->logs->log("Gdtot","GDtot is Added in file");
                return;
            }
            
            $this->logs->log("Gdtot","Error in Adding Gdtot");
            return;

            
        }
                
        $this->logs->log("Gdtot","GDtot is present in file");
    }
    
    
    public function uploadHubcloud($api) {
        
        $hubcloudExists = $this->file->checkServer(16);

        if($hubcloudExists) {
            $this->logs->log("Hubcloud", "Already Exists");
            return;
        }

        $slug = Hubcloud::upload($api,$this->file->driveId());
        
        if($slug['status'] == "success") {
            $this->file->storeServer(16,$slug['message'],$api);
            $this->logs->log("Hubcloud", "Added in File");
        } else {
            $this->logs->log("Hubcloud", "Error in uploading . " . json_encode($slug['message']));
        }
        
    }

    public function uploadEarnvids ($api) {

        $earnVidsExist = $this->file->checkServer(7);
        
        if($earnVidsExist) {

            $this->logs->log("Earnvids","Earnvids is present in file");
            
            $isWorking = EarnVids::isWorking($earnVidsExist['api'],$earnVidsExist['slug']);

            if($isWorking['status'] == "success") {$this->logs->log("Earnvids","Earnvids is Working");return;} 
            else {
    
                $this->logs->log("Earnvids","Earnvids is Broken");
                
                $this->file->deleteServer(7);
                $this->logs->log("Earnvids","Earnvids is Deleted From file");

            }
        }

        if($this->isDownloadAble) {
            $slug = EarnVids::upload($api,$this->file->directUrl());
            if($slug['status'] == "success") {
                $this->file->storeServer(7,$slug['message'],$api);
                $this->logs->log("Earnvids","Earnvids Added in file " . $slug['message']);
            } else {
                $this->logs->log("Earnvids","Earnvids is not Uploaded " . $slug['message']);
            }
        }
    }
    
    public function uploadStreamHG ($api) {
        
        $streamhgExists = $this->file->checkServer(6);
        
        if($streamhgExists) {
            
            $this->logs->log("Streamhg","Streamhg is present in file");
            
            $isWorking = StreamHG::isWorking($streamhgExists['api'],$streamhgExists['slug']);
            
            if($isWorking['status'] == "success") {$this->logs->log("Streamhg","Streamhg is working");return;}
            else {
                $this->logs->log("Streamhg","Streamhg is broken");
                $this->file->deleteServer(6);
                $this->logs->log("Streamhg","Streamhg is Deleted From File");
            }
        }

        if($this->isDownloadAble) {
            $slug = StreamHG::upload($api,$this->file->directUrl());
            if($slug['status'] == "success") {
                
                $this->file->storeServer(6,$slug['message'],$api);
                $this->logs->log("Streamhg","Streamhg is Added in File " . $slug['message']);
                return;
            } else {
                $this->logs->log("Streamhg","Streamhg is Not upload " . $slug['message']);
            }
        }
    }

    
    public function uploadFileMoon($api) {

        $filemoonExists = $this->file->checkServer(5);
        
        if($filemoonExists) {
            
            $this->logs->log("Filemoon", "Filemoon Exists in File");
            
            $isWorking = Filemoon::isWorking($filemoonExists['api'],$filemoonExists['slug']);
            
            if($isWorking['status'] == "success") {$this->logs->log("Filemoon", "Filemoon is Working");return;}
            else {
                    $this->logs->log("Filemoon", "Filemoon is Broken");
                    $this->file->deleteServer(5);
                    $this->logs->log("Filemoon", "Filemoon is Deleted From File");
            }
    }

        if($this->isDownloadAble) {
            $slug = Filemoon::upload($api,$this->file->directUrl());
            if($slug['status'] == "success") {
                
                $this->file->storeServer(5,$slug['message'],$api);
                $this->logs->log("Filemoon", "Filemoon is Added in File " . $slug['message']);
                return;
            } else {
                $this->logs->log("Filemoon", "Filemoon not Uploaded " . $slug['message'] . " " . $api);
            }
        }
    }
 
    public function uploadPlayerx($api) {
        
        if($this->isInGetStatus(2)) {$this->logs->log("Playerx", "Video is in GetStatus Queue"); return;}
        
        $playerxExists = $this->file->checkServer(2);
    
        if ($playerxExists) {
            
            $this->logs->log("Playerx", "ALready Available in File");
            
            $isWorking = Playerx::isWorking($playerxExists['slug'], $playerxExists['api']);
            
            if ($isWorking['status'] == "error") {
                $this->logs->log("Playerx", "Playerx Link is not Working");
                $this->file->deleteServer(2);
                $this->logs->log("Playerx", "Playerx Deleted From File");
            } else {$this->logs->log("Playerx", "Playerx is Working"); return;}
        }

        if($this->isDownloadAble) {
            
            $slug = Playerx::upload($api, $this->file->directUrl());
        
            if ($slug['status'] == "success") {
                
                $this->addinGetStatus(2, $slug['message'], $api);
                
                $this->logs->log("Playerx", "Playerx Added in Get Status");
    
            } else { $this->logs->log("Playerx",$slug['message']); }
        }
    
    }
    
    public function uploadVoe($api) {
        
        $voeExists = $this->file->checkServer(9);
        
        if($voeExists) {
            
            $this->logs->log("Voe","Voe Already Present in File");
            
            $isWorking = Voe::isWorking($voeExists['api'],$voeExists['slug']);
            
            if($isWorking['status'] == "success") {$this->logs->log("Voe","Voe is Working " . $isWorking['message']);return;}
            else {
                $this->logs->log("Voe","Voe is Broken");
                $this->file->deleteServer(9);
                $this->logs->log("Voe","Voe is Deleted From File");
            }
        }

        if($this->isDownloadAble) {
            $slug = Voe::upload($api,$this->file->directUrl());
            if($slug['status'] == "success") {
                $this->logs->log("Voe","Voe is Uploaded " . $slug['message']);
                $this->file->storeServer(9,$slug['message'],$api);
            } else {
                $this->logs->log("Voe","Voe not Uploaded " . $slug['message']);

            }
        }

    }
    
        
    public function uploadHydrax($api) {
        
        if(!$this->file->checkServer(14)) {
            
            $slug = Hydrax::upload($api,$this->file->driveId());
            
            if($slug['status'] == "success") {
                
                $this->file->storeServer(14,$slug['message'],$api);
                $this->logs->log("Hydrax", "Hydrax Added in File");
                return;
            } else {
                $this->logs->log("Hydrax", "Hydrax Not Uploaded " . $slug['message']);
                return;
            }
        }
        
        $this->logs->log("Hydrax", "Hydrax is present in File");

    }

    
}
