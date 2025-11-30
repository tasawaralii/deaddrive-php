<?php

class Pack {
    private $db = null;
    private $query = "SELECT li.link_id,li.Id,li.uid,li.Name FROM links_info li 
                    LEFT JOIN servers_links sl ON li.link_id = sl.link_id AND sl.server_id = :server_id 
                    WHERE li.live <> 0 AND li.Type = 'zip' AND li.user = 2 AND sl.slug IS NULL 
                    ORDER BY li.link_id DESC LIMIT ";
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function notHubcloud() {
        $this->query .= "1";
        $res = $this->db->prepare($this->query);
        $res->execute([':server_id' => 16]);
        $res = $res->fetchAll();
        // print_r($res);
        
        foreach($res as $p) {
            $file = new File($this->db);
            $file->setLinkId($p['link_id']);
            
            if(!$file->isFileDownloadable()) {
                echo "File Not DownloadAble " . $p['Id'];
                $file->setBroken();
                continue;
            }
            
            $slug = Hubcloud::upload("ekN2aFdQbSs5dGpYN0RPNVhmNUVCdz09",$p['Id']);
            print_r($slug);

            if($slug['status'] == "success") {
                $file->storeServer(16,$slug['message'],"ekN2aFdQbSs5dGpYN0RPNVhmNUVCdz09");
                echo "Hubcloud Uploaded in " . "https://deaddrive.shop/file/" . $p['uid'];
            } else {
                print_r($slug);
                echo " Google Drive File ID : " . $p['Id'];
            }
        }
    }
    
    public function notGdtot() {
        // $this->query .= "10"; // taking much time on more than 1
        // $res = $this->db->prepare($this->query);
        
        $res = $this->db->prepare("SELECT li.link_id,li.Id,li.uid,li.Name FROM links_info li 
                    LEFT JOIN servers_links sl ON li.link_id = sl.link_id AND sl.server_id = :server_id 
                    WHERE li.live <> 0 AND li.user = 2 AND sl.slug IS NULL 
                    ORDER BY li.link_id DESC LIMIT 5");
        
        $res->execute(['server_id' => 11]);
        $res = $res->fetchAll();
        
        foreach($res as $p) {
            $file = new File($this->db);
            $file->setLinkId($p['link_id']);
            
            if(!$file->isFileDownloadable()) {
                echo "File Not DownloadAble " . $p['Id'];
                $file->setBroken();
                continue;
            }
            
            $slug = Gdtot::upload($file->driveId(),GDTOT_DEFAULT_API['api'],GDTOT_DEFAULT_API['gmail']);
            
            if(!isset($slug['status'])) {
                echo "Error";
                print_r($slug);
                continue;
            }
            if($slug['status'] == "success") {
                $file->storeServer(11,$slug['message'],GDTOT_DEFAULT_API['api']);
                echo "GDToT Added " . "https://deaddrive.shop/file/" . $p['uid'] ."<br>";
            } else {
                print_r($slug);
            }
        }
    }
    
    public function notGdflix() {
        $this->query .= "200";
        $res = $this->db->prepare($this->query);
        $res->execute(['server_id' => 18]);
        $res = $res->fetchAll();
        $driveLinks = [];
        
        foreach($res as $p) {
            $driveLinks[] =  ['name' => $p['Name'],'drive_link' => "https://drive.google.com/file/d/". $p['Id'] ."/view?usp=drive_link"];
        }
        
        return $driveLinks;
    }
    
    
    public function notAppdrive() {
        $this->query .= "50";
        $res = $this->db->prepare($this->query);
        $res->execute(['server_id' => 3]);
        $res = $res->fetchAll();
        $driveLinks = [];
        
        foreach($res as $p) {
            $driveLinks[] =  ['name' => $p['Name'],'drive_link' => "https://drive.google.com/file/d/". $p['Id'] ."/view?usp=drive_link"];
        }
        
        return $driveLinks;
    }
    
    public function notNeodrive () {
        $this->query .= "1";
        $res = $this->db->prepare($this->query);
        $res->execute(['server_id' => 17]);
        $res = $res->fetchAll();
        foreach($res as $p) {
            $file = new File($this->db);
            $file->setLinkId($p['link_id']);
            $slug = Neodrive::upload($file->driveId(),'53e0b0e686f4f107d1b0c54d58754a4d','deadtoons06@gmail.com');
            if($slug['status'] == "success") {
                $file->storeServer(17,$slug['message'],'53e0b0e686f4f107d1b0c54d58754a4d');
                echo "Neo Drive Added in " . "https://deaddrive.shop/file/" . $p['uid'] ."<br>";
            }
        }
    }
    
    public function notFilepress() {
        $this->query .= "5";
        $stmt = $this->db->prepare($this->query);
        
        $stmt->execute(['server_id' => 1]);
        $res = $stmt->fetchAll();
        foreach($res as $r) {
            $file = new File($this->db);
            $file->setLinkId($r['link_id']);
            $slug = Filepress::upload("/1Y9tekNZgjl7FUjHPBPfeg596RgDqtrCnbkrBPqBWY=",$file->driveId());
            
            if($slug['status'] == "success") {
                $file->storeServer(1,$slug['message'], "/1Y9tekNZgjl7FUjHPBPfeg596RgDqtrCnbkrBPqBWY=");
                echo "Filepress Drive Added in " . "https://deaddrive.shop/file/" . $r['uid'] ."<br>";
            }
        }
    }
    
}