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
        // print_r($response);
        
        if (curl_errno($ch)) {
            echo "cURL Error: " . curl_error($ch);
        } else {
            $response_data = json_decode($response, true);
            
            if ($response_data['status']) {
                // print_r($response_data);
                return ['status' => "success", 'message' => $response_data['data']['share_id']];
            } else {
                print_r($response_data);
                return ['status' => "error", 'message' => $response];
            }
        }

        curl_close($ch);
    }
}
