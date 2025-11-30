<?php
require_once("../../config.php");
class fetch {
    
    public static function request($url) {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // return response to script instead of browser
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification (optional)
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if($httpCode != 200 || $result === false) {
            return ["status" => "error", "message" => "Request failed"];
        }

        $response = json_decode($result, true);

        if (!$response) {
            return ["status" => "error", "message" => "Invalid API response"];
        }
        return $response;
    }
}

class Hubcloud {
    private $api;
    
    public function __construct($api) {
        $this->api = $api;
    }
    
    public function validateApi() {
        $request = HUBCLOUD_API_URL . "/drive/shareapi.php?key=".$this->api;
        $response = fetch::request($request);
        if($response['status'] == 400) {
            return false;
        }
        return true;
    }
    public function upload($drive_id) {
        $request = HUBCLOUD_API_URL . "/drive/shareapi.php?key=".$this->api."&link_add=$drive_id";
        $response = fetch::request($request);
        if($response['status'] == "200") {
            $slug = $response['data'];
            return $slug;
        }
    }
}

$hubcloud = new Hubcloud("ekN2aFdQbSs5dGpYN0RPNVhmNUVCdz0");

// echo $hubcloud->validateApi();
echo $hubcloud->upload("1VWaqwJXRCpd6t0ZZ-cVas_woQb_mVxM6");
