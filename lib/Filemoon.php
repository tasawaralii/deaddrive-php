<?php

class Filemoon {
    
    private static function fetch($endpoints) {
        return Fetch::getRequest(FILEMOON_API_URL . $endpoints);
    }

    public static function checkApi($api) {
        
        $response = self::fetch("/account/info?key=" . $api);
        
        if($response['status'] == "success") {
            $response = $response['message'];
            
            if ($response['status'] == 200) {
                return ['status' => "success", 'message' => $response['result']['login']];
            } else {
                return ['status' => "error", 'message' => $response['msg']];
            }
        }

        return false;
    }

    public static function upload($api, $remoteUrl) {
        
        $response = self::fetch("/remote/add?key=" . $api . "&url=" . urlencode($remoteUrl));
        
        if($response['status'] == "success"){
            
            $response = $response['message'];
            
            if ($response['status'] == 200) {
                
                $slug = $response['result']['filecode'];
                return ['status' => "success", 'message' => $slug];
                
            } else {
                return ['status' => "error", 'message' => $response['msg']];
            }
        }
    }

    public static function isWorking($api, $slug) {
        
        $response = self::fetch("/file/info?key=" . $api . "&file_code=" . $slug);
        
        if($response['status'] == "success") {
            $response = $response['message']['result'][0];
            if ($response['status'] != 200) {
                return ['status' => "error",'message' => $response['msg']];
            }
            return ['status' => "success", 'message' => $response];
        }
            
    }
}