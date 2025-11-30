<?php
class Fetch {
    
    public static function getRequest($url) {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return response to script instead of browser
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification (optional)
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if($httpCode != 200 || $result === false) {
            return ['status' => "error", "message" => $result];
        }

        $response = json_decode($result, true);

        if (!$response) {
            return ['status' => "error",'message' => "Not a Json Response"];
        }
        
        return ['status' => "success", 'message' => $response];
    }
}
