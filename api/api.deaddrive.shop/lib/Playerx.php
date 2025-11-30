<?php

class Playerx {
    
    public static function upload($api,$url) {
        
        $request = Fetch::getRequest(PLAYERX_API_URL . "?api_key=".$api."&url=".$url."&action=add_remote_url");

        if($request['status'] == "success") {
            
            $request = $request['message'];
            
            if(!$request['result'] == true) {
                return ['status' =>"error", "message" => $request['reason']];
            }
            
            $playerxUrl = $request['player'];
            
            preg_match("/https:\/\/[a-z]+\.[a-z]+\/v\/([a-zA-Z0-9]+)\//", $playerxUrl, $slug);
            
            if($slug) {
                return ['status' => "success", 'message' => $slug[1]];
            }
        }
        return ['status' => "error", "message" => "Api Not Working"];
    }
    
    public static function isWorking($slug,$api) {
        
        $request = PLAYERX_API_URL . "?slug=".$slug."&api_key=".$api."&action=detail_video";
        $request = Fetch::getRequest($request);
        
        if($request['status'] == "success") {
            $request = $request['message'];
            if($request['result'] == false) {
                if(strpos($request['reason'],"not found") !== false) {
                    return ['status' => "error", "message" => $request['reason']];
                } else {
                    return ['status' => "success", 'message' => $request['reason']];
                }
            }
            return ['status' => "success", 'message' => $request['title']];
        }
    }
    
    static public function checkStatus($slug,$api) {
        
        $request = PLAYERX_API_URL . "?slug=".$slug."&api_key=".$api."&action=detail_video";
        $request = Fetch::getRequest($request);
        
        if($request['status'] == "success") {
            $request = $request['message'];
            if($request['result'] == true) {
                return ['status' => "success", "message" => $request['title']];
            } else {
                return ['status' => "error", 'message' => $request['reason']];
            }
        } 
        
        return ['status' => "error", 'message' => $request['message']];
        
    }
}