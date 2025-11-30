<?php

class StaticClass {
    
    public static function DefaultApis($server_id) {
        // echo "Default Api <br>";
        switch($server_id) {
            case 1:
                return FILEPRESS_DEFAULT_API;
                break;
            case 4:
                return SEND_DEFAULT_API;
                break;
            case 5:
                return FILEMOON_DEFAULT_API;
                break;
            case 6:
                return STREAMHG_DEFAULT_API;
                break;
            case 7:
                return EARNVIDS_DEFAULT_API;
                break;
            case 9:
                return VOE_DEFAULT_API;
                break;
            case 14:
                return HYDRAX_DEFAULT_API;
                break;
            case 16:
                return false;
            default :
                return null;
        }
    }
    
    
    public static function dieError($message) {
        die(json_encode(['status' => 'error', 'message' => $message]));
    }

    public static function dieSuccess($data) {
        echo json_encode(array_merge(['status' => 'success'], $data));
        exit;
    }
    
    
    public static function isFileDownloadable($url) {
        
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_NOBODY, true);  // Only get the headers
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);  // Return headers
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);  // Set a timeout

    curl_exec($ch);
    
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return ($httpCode == 200);
    
    }
    
    public static function responseError($message) {
        die(json_encode(['status' => 'error', 'message' => $message]));
    }

    public static function responseSuccess($data) {
        echo json_encode(array_merge(['status' => 'success'], $data));
        exit;
    }


    
}
