<?php

class Send {
    public static function checkApi($api) {
        $request = SEND_API_URL . "/upload/url?key=".$api."&url=https://google.com";
        $fetch = Fetch::getRequest($request);
        if($fetch['status'] == "success") {
            $fetch = $fetch['message'];
            if($fetch['status'] == 200) {
                return ['status' => "success",'message' => $fetch['result']['filecode']];
            }
        }
        return ['status' => "error", 'message' => $fetch];
    }
    public static function upload($api,$directUrl) {
        $request = SEND_API_URL . "/upload/url?key=". $api . "&url=" . $directUrl;
        $fetch = Fetch::getRequest($request);
        
        if($fetch['status'] == "success") {
            
            $fetch = $fetch['message'];
            
            if($fetch['status'] == 200) {
                $slug = $fetch['result']['filecode'];
                return ['status' => "success", 'message' => $slug];
            } else {
                return ['status' => "error", 'message' => $fetch['msg']];
            }
        }
        
        return $fetch;
    }
    public static function isWorking($api,$slug) {
        $request = SEND_API_URL . "/file/info?key=" . $api . "&file_code=" . $slug;
        $fetch = Fetch::getRequest($request);
        
        if($fetch['status'] == "success") {
            $fetch = $fetch['message'];
            if($fetch['status'] == 200) {
                if($fetch['result'][0]['status'] == 200) {
                    return ['status' => "success", 'message' => $fetch['result'][0]['name']];
                }
            }
        }
        return ['status' => "error", 'message' => $fetch['msg']];
    }
}