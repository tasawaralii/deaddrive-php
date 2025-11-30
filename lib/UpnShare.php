<?php

class UpnShare {
    
    private $api;
    private $apiDomain;
    
    public function __construct($apiDomain, $api) {
        $this->apiDomain = $apiDomain;
        $this->api = $api;
    }
    
    public function checkApi() {
        $endpoint = "/api/v1/user/information";
        $response = $this->apiRequest($endpoint,"get",[]);
        if(isset($response['message']) && $response['message'] == "Unauthorized")
            return ['status' => "error", "message" => "Wrong Api Key"];
        return ['status' => "success", 'message' => $response['name']];
    }
    
    public function upload($url,$name) {
        $endpoint = "/api/v1/video/advance-upload";
        
        $data = [
            "url" => $url,
            "name" => $name 
        ];
        
        $response = $this->apiRequest($endpoint,"post",$data);
        if(isset($response['id'])) {
            return ['status' => "success", 'message' => $response['id']];
        }
        return ['status' => "error", "message" => $response];
    }
    
    public function getUploadStatus($id) {
        $endpoint = "/api/v1/video/advance-upload/$id";
        $response = $this->apiRequest($endpoint,"get");
        if(isset($response['status']) && $response['status'] == "Completed") {
            
            if(!isset($response['videos'][0])) {
                return ['status' => 'error', 'message' => json_encode($response)];
            }
            
            return ['status' => "success", "message" => "Video Uploaded Successfully",'slug' => $response['videos'][0],'response' => $response];
        }
        return ['status' => 'error', 'message' => "Some Error" , 'response' => $response];
    }
    
    public function getPlayStatus($slug) {
        
        $slug = str_replace("#",'',$slug);
        
        $endpoint = "/api/v1/video/manage/$slug";
        $response = $this->apiRequest($endpoint,"get");
        if(isset($response['status']) && $response['status'] == "Active") {
            return ['status' => 'success', 'message' => 'Video is Ready', 'response' => $response];
        }
        return ['status' => 'error', 'message' => (isset($response['status']) ? $response['status'] : $response)];
    }
    
    private function apiRequest($endpoint,$method = "get",$data = []) {
        
        $ch = curl_init();
        
        curl_setopt($ch,CURLOPT_URL,$this->apiDomain.$endpoint);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        
        curl_setopt($ch,CURLOPT_HTTPHEADER,[
            "api-token: ".$this->api,
            "accept: application/json",
            "Content-Type: application/json"
            ]);

        if($method == "post") {
            curl_setopt($ch,CURLOPT_POST,true);
            curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($data));
        }
        
        $response = curl_exec($ch);
        
        curl_close($ch);
        
        return json_decode($response,true);
    }
}
