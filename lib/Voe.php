<?php
class Voe {
    
    private static function fetch($endpoints) {
        return Fetch::getRequest(Voe_API_URL . $endpoints);
    }
    
    static public function checkApi($api) {
        
        $response = self::fetch("/account/info?key=" . $api);

        if($response['status'] == "success") {
            
            $response = $response['message'];
            
            if ($response['status'] == 200) {
                return ['status' => "success", 'message' => $response['result']['email']];
            } else {
                return ['status' => "error", 'message' => $response['msg']];
            }
        }
        return ['status' => "error",'message' => $response['message']];
    }
    
    static public function upload($api,$remoteUrl) {
        
        $response = self::fetch("/upload/url?key=" .$api . "&url=" . str_replace(' ', '+', $remoteUrl));
        
        if($response['status'] == "success"){
            
            $response = $response['message'];
            
            if ($response['status'] == 200) {
                
                $slug = $response['result']['file_code'];
                return ['status' => "success", 'message' => $slug];
                
            } else {
                return ['status' => "error", 'message' => $response['msg']];
            }
        }
        return ['status' => "error", 'message' => $response['message']];
    }
    
    public static function isWorking($api,$slug) {

        $response = self::fetch("/file/info?key=" . $api . "&file_code=" . $slug);
        
        if($response['status'] == "success") {
            $response = $response['message'];
            if ($response['status'] != 200) {
                return ['status' => "error",'message' => $response['msg']];
            }
            return ['status' => "success", 'message' => json_encode($response)];
        }
        
        return $response;
    }

}
