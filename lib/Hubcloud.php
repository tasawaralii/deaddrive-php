<?php
class Hubcloud {
    
    static public function checkApi($api) {
        
        $request = HUBCLOUD_API_URL . "/drive/shareapi.php?key=".$api;
        
        $response = Fetch::getRequest($request);
        if($response['status'] == "success") {
            $response = $response['message'];
            if($response['status'] == 400) {
                return ['status' => "error", "message" => "Api not Correct"];
            }
        }
        return ['status' => "success", "message" => "Api Working"];
    }
    
    static public function upload($api,$drive_id) {
        
        $request = HUBCLOUD_API_URL . "/drive/shareapi.php?key=".$api."&link_add=$drive_id";
        // echo $request;
        // echo $request;
        $response = Fetch::getRequest($request);
        
        if($response['status'] == "success") {
            
            $response = $response['message'];
            
            if($response['status'] == "200") {
                
                $slug = $response['data'];
                return ['status' => "success", 'message' => $slug];
                
            } else {
                
                return ['status' => "error", 'message' => $response];
                
            }
            
        } else {
            return $response;
        }
    }
}
