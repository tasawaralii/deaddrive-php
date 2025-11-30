<?php

class Filepress {
    
    public static function upload($apiKey, $driveId) {
        $domain = 'https://' . FILEPRESS_DOMAIN . '/api/v1/file/add';
        $data = json_encode([
            'key' => $apiKey,
            'id' => $driveId,
            'isAutoUploadToStream' => true
        ]);

        $ch = curl_init($domain);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ],
            CURLOPT_FOLLOWLOCATION => true
        ]);

        $response = curl_exec($ch);
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($httpCode != 200) {
            return ['status' => 'error', 'message' => json_decode($response,true) ?? "Api Not Working"];
        }

        $result = json_decode($response, true);
        
        if(isset($result['data']['_id'])) {
            
            return ['status' => 'success', 'message' => $result['data']['_id']];
        }
        return ['status' => 'error', 'message' => $result['error'] ?? "Api Not Working"];
    }
    
}
