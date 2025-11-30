<?php

class Queue {
    private $db = null;
    private $queueId = null;
    private $link_id = null;
    
    private $uid = null;
    
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
    
    public function setUid($uid) {
        
        if(!$this->uid) {
            $this->uid = $uid;
            return true;
        }
        return false;
    }
    
    public function setup() {

        $this->file = new File($this->db);
        
        if(!$this->uid) {
            if(!$this->link_id) { 
                return ['status' => "error", 'message' => "No LinkId Available"];
            } else {
                $this->file->setLinkId($this->link_id);
            }
        } else {
            $this->file->setUid($this->uid);
            $this->link_id = $this->file->getLinkId();
        }
        
        
        $this->logs = new Log();

        $this->logs->log("Name", $this->file->getName());
                
        // $this->logs->log("Direct Url", $this->file->directUrl());
                
        $this->isDownloadAble = $this->file->isFileDownloadable();
        
        if(!$this->isDownloadAble) { $this->logs->log("File Status", "File is not Downloadable");}
        
        
        $user_id = $this->file->user_id();
        
        $this->user = new User($this->db);
        $this->user->setUserId($user_id);
        
    }
    public function getlogs() {return $this->logs->getlogs();}
    
    private function processQueue($mode) {

        if($mode == "zip") {
            $user_apis = $this->user->UserApis("zip");
        } else if($mode == "enabled") {
            $user_apis = $this->user->UserApis("enabled");
        } else if($mode == "all") {
            $user_apis = $this->user->UserApis("all");
        }

        $this->processServers($user_apis);
        
        $this->file->storeLogs($this->getlogs());
        $this->removeFromQueue();
        
    }
    
    public function processAllServers() {
        $this->setup();

        $this->processQueue("all");
        return $this->logs->getlogs();
    }
    
    public function processEnabledServers() {
        $this->setup();
        if($this->file->isZip()) {
            $this->processQueue("zip");
        } else {
            $this->processQueue("enabled");
        }
        return $this->logs->getlogs();
    }
    
    public function processZipServers() {
        $this->setup();
        $this->processQueue("zip");
        return $this->logs->getlogs();
    }
    
    private function processServers($servers) {
        
        foreach($servers as $enabledServer) {
            
            $server_id = $enabledServer['server_id'];
            
            $api = $enabledServer['api'];
            $email = $enabledServer['email'];

            if(!$api || $api == "") {
                $api = StaticClass::DefaultApis($server_id,"api");
                $email = StaticClass::DefaultApis($server_id,"email");
            }
            
            if(!$api) {
                $this->logs->log($enabledServer['server_name'], "No Api Available");
                continue;
            }

            if($server_id == 1) {
                
                $this->uploadFilepress($api);
                
            } else if($server_id == 2) {
                
                
                
                // $this->uploadPlayerx($api);
                
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
                
                $this->uploadGdtot($api,$email);
                
            } else if($server_id == 14) {
                
                $this->uploadHydrax($api);
                
            } else if($server_id == 16) {
                if(!$api) {
                    $this->logs->log("Hubcloud", "Api not Available");
                    continue;
                }
                $this->uploadHubcloud($api);
            } else if($server_id == 17) {
                $this->uploadNeoDrive($api,$email);
            } else if($server_id == 19) {
                $this->uploadUpn($api);
            } else if($server_id == 20) {
                $this->uploadRpm($api);
            }
            
        }
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
        
        $stmt = $this->db->prepare("INSERT INTO getstatus (link_id, server_id, slug_id, api) VALUES (:link_id, :server_id, :slug, :api)");
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
    
    public function uploadUpn($api) {
        
               
        if($this->isInGetStatus(19)) {
            $this->logs->log("Upn", "Video is in GetStatus Queue");
            return;
        }
        
        $upn = new UpnShare(UPNSHARE_API_URL,$api);
        
        $upnExists = $this->file->checkServer(19);
        
        if($upnExists) {
            $this->logs->log("Upn", "Upn is already Present");
            $isWorking = $upn->getPlayStatus($upnExists['slug']);
            if($isWorking['status'] == "success") {
                $this->logs->log('Upn','Upn is Working');
                return;
            } else {
                $this->logs->log("Upn", "Upn is broken");
                $this->file->deleteServer(19);
                $this->logs->log("Upn", "Upn is deleted");
            }
        }
        
        if($this->isDownloadAble) {
            $videoId = $upn->upload(str_replace(" ", "+", $this->file->directUrl()),$this->file->getName());
            if($videoId['status'] == "success") {
            
                if($videoId['message']) {
                    $this->addinGetStatus(19, $videoId['message'], $api);
                    $this->logs->log("Upn","Added In Get Status Queue");
                } else {
                    $this->logs->log("Upn", "Empty Slug ". $videoId);
                }
            

            } else {
                $this->logs->log("Upn","Error in Uploading " . json_encode($videoId));
            }
        } else {
            $this->logs->log("Upn","File is Not Downloadable");
        }
    }
    
    public function uploadRpm($api) {
        
               
        if($this->isInGetStatus(20)) {
            $this->logs->log("Rpm", "Video is in GetStatus Queue");
            return;
        }
        
        $rpm = new RpmShare(RPMSHARE_API_URL,$api);
        
        $rpmExists = $this->file->checkServer(20);
        
        if($rpmExists) {
            $this->logs->log("Rpm", "Rpm is already Present");
            $isWorking = $rpm->getPlayStatus($rpmExists['slug']);
            if($isWorking['status'] == "success") {
                $this->logs->log('Rpm','Rpm is Working');
                return;
            } else {
                $this->logs->log("Rpm", "Rpm is broken");
                $this->file->deleteServer(20);
                $this->logs->log("Rpm", "Rpm is deleted");
            }
        }
        
        if($this->isDownloadAble) {
            $videoId = $rpm->upload(str_replace(" ", "+", $this->file->directUrl()),$this->file->getName());
            if($videoId['status'] == "success") {
            
                if($videoId['message']) {
                    $this->addinGetStatus(20, $videoId['message'], $api);
                    $this->logs->log("Rpm","Added In Get Status Queue");
                } else {
                    $this->logs->log("Rpm", "Empty Slug ". $videoId);
                }
            

            } else {
                $this->logs->log("Rpm","Error in Uploading " . json_encode($videoId));
            }
        } else {
            $this->logs->log("Rpm","File is Not Downloadable");
        }
    }

    
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
    // Check if file already exists on Filepress (server ID 1)
    $filepressExists = $this->file->checkServer(1);

    // If not already uploaded
    if (!$filepressExists) {
        // Attempt to upload to Filepress
        $slug = Filepress::upload($api, $this->file->driveId());

        // Check if response is valid and successful
        if (is_array($slug) && isset($slug['status']) && $slug['status'] === "success") {
            // Store the new Filepress link on server 1
            $this->file->storeServer(1, $slug['message'], $api);
            $this->logs->log("Filepress", "Upload successful and stored in file.");
            return;
        } else {
            // Upload failed or invalid response
            $this->logs->log("Filepress", "Upload failed.");
            $this->logs->log("Filepress", "Upload response: " . json_encode($slug));
            return;
        }
    }

    // If already exists
    $this->logs->log("Filepress", "Already exists in file.");
    // $this->logs->log("Filepress", "checkServer(1) result: " . json_encode($filepressExists));
}

    
    public function uploadNeoDrive($api,$email) {
        if(!$this->file->checkServer(17)) {
            $slug = Neodrive::upload($this->file->driveId(),$api,$email);
            if($slug['status'] == "success") {
                $this->file->storeServer(17,$slug['message'],$api);
                $this->logs->log("Neodrive", "NeoDrive Added In File");
                return;
            } else {
                $this->logs->log("Neodrive", $slug['message']);
                return;
            }
        }
        $this->logs->log("Neodrive", "Neo Drive Already Exists");
    }
    
    public function uploadGdtot($api,$email) {
        
        if(!$this->file->checkServer(11)) {
    
            $slug = Gdtot::upload($this->file->driveId(),$api,$email);
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
            
            if($isWorking['status'] == "success") {$this->logs->log("Filemoon", "Filemoon is Working ");return;}
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
        } else {
            $this->logs->log("Filemoon", "Filemoon deleted From File");
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
                
                if($slug['message']) {
                    $this->addinGetStatus(2, $slug['message'], $api);
                    
                    $this->logs->log("Playerx", "Playerx Added in Get Status slug: ". json_encode($slug));
                    
                } else {
                    $this->logs->log("Playerx", "Slug is Empty ".$slug);
                }
    
            } else { $this->logs->log("Playerx",$slug['message']); }
        }
    
    }
    
    public function uploadVoe($api) {
        
        $voeExists = $this->file->checkServer(9);
        
        if($voeExists) {
            
            $this->logs->log("Voe","Voe Already Present in File");
            
            $isWorking = Voe::isWorking($voeExists['api'],$voeExists['slug']);
            
            if($isWorking['status'] == "success") {$this->logs->log("Voe","Voe is Working ");return;}
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
                $message = json_decode($slug['message'],true);
                $this->logs->log("Voe","Voe not Uploaded " . ($message ? json_encode($message) : ""));

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
