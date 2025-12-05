<?php
class GoogleDrive {

    public static function fetchFileInfo($driveId) {

        $url = "https://www.googleapis.com/drive/v3/files/{$driveId}?fields=*&supportsAllDrives=True&key=". $_ENV['GOOGLE_API'];

        $data = Fetch::getRequest($url);

        if ($data['status'] == "success") {
            return $data;
        }
        return ['status' => "error", 'message' => json_decode($data['message'],true)];
    }
    
    public static function fetchFilesFromFolder($folderId) {

    $apiUrl = "https://www.googleapis.com/drive/v3/files?q=%27{$folderId}%27+in+parents&fields=files(id,webViewLink,mimeType)&key=" .$_ENV['GOOGLE_API'] ."&orderBy=name&supportsAllDrives=True&includeItemsFromAllDrives=True";
    
    $response = Fetch::getRequest($apiUrl);
    
    $files = [];
    
    if ($response['status'] != 'success') {
        return $files;
    }
    
    $data = $response['message'];
    
    if (isset($data['files']) && count($data['files']) > 0) {
        
        foreach ($data['files'] as $file) {
            
            if ($file['mimeType'] != 'application/vnd.google-apps.folder') {

                $files[] = ['webViewLink' => $file['webViewLink']];
                continue;
                
            }
            
            $subfolderFiles = self::fetchFilesFromFolder($file['id']);
            
            if ($subfolderFiles)
                $files = array_merge($files, $subfolderFiles);
        }
    }
    
    return $files;
    
    }
}
