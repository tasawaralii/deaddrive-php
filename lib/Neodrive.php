<?php

class Neodrive {
    
    public static function upload($drive_id,$api,$gmail) {

        $data = array(
            'email' => $gmail,
            'api_token' => $api,
            'url' => $drive_id
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, NEODRIVE_API_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/x-www-form-urlencoded"
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return ['status' => 'error' , 'message' => curl_error($ch)];
        } else {
            $response_data = json_decode($response, true);
            // echo $response;
            
            if ($response_data['status']) {

                return ['status' => "success", 'message' => $response_data['data']['share_id']];
                
            } else {
                return ['status' => "error", 'message' => $response_data['message']];
            }
        }

        curl_close($ch);
    }
    public static function checkApi($api,$gmail) {
        return self::upload(DEFAULT_GOOGLE_DRIVE_ID,$api,$gmail);
    }
}
