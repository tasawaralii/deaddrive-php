<?php

class Hydrax {
    
    public static function upload($apikey,$drive_id) {

        $url = "https://api.hydrax.net/$apikey/drive/$drive_id";
        
        $result = Fetch::getRequest($url);
    
        if ($result['status'] == "success") {
            $result = $result['message'];
            
            if(isset($result['slug'])) {
                return ['status' => "success", 'message' => $result['slug']];
            }
    
            return ['status' => "error", 'message' => isset($result['msg']) ? $result['msg'] : "Error"];

        }
        return $result;
    }
}
