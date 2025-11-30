<?php

class Gdtot {
    
    public static function upload($drive_id,$api,$gmail) {
        
        $url = "https://drive.google.com/file/d/{$drive_id}/view?usp=drive_link";
        
        $data = array(
        "email" => $gmail,
        "api_token" => $api,
        "url" => $url,
        );
        
        
        $jsonData = json_encode($data);
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, GDTOT_API_URL); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ));
        
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            curl_close($ch);
            return $response;
        }
        
        $response = json_decode($response, true);

        if($response) {
            if(isset($response['data'][0]['id'])) {
                $slug = $response['data'][0]['id'];
                curl_close($ch);
                return ['status' => "success", 'message' => $slug];
            } else {
                return ['status' => "error", 'message' => $response['message']];
            }
        }
        
        curl_close($ch);
        
        return ['status' => "error", 'message' => $response];
    
    }
}

