<?php

function showAddWebsiteHTML()
{

    $html = "";

    $html .= "<center>";

    $html .= '<strong>Please Enter Your Site Name And Url at <i><a href="/account" style="color:royalBlue">Account</a></i> before sharing files.</strong><hr>';

    $html .= '<h6>**Instructions**</h6>';

    $html .= "</center>";

    $html .= "<ol>";

    foreach (INSTRUCTIONS as $ins) {
        $html .= '<li>' . $ins . '</li>';
    }

    $html .= "</ol>";

    return $html;
}

function fetchContent($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "cURL Error: " . curl_error($ch);
        return false;
    }

    curl_close($ch);
    return $response;
}

function checklogin()
{
    if (!isset($_COOKIE['ddeml'])) {
        header("Location: / ");
        exit;
    }
}

function userinfo($email, $pdo, $total = false)
{

    $info = $pdo->query("SELECT * FROM users WHERE users.email = '$email'")->fetch(PDO::FETCH_ASSOC);

    if ($total) {
        $res = $pdo->query("
    SELECT 
    COUNT(*) AS total_files, 
    SUM(size) AS total_size, 
    SUM(downloads) AS downloads, 
    SUM(views) AS views, 
    SUM(CASE WHEN links_info.live = 0 THEN 1 ELSE 0 END) AS broken
FROM 
    links_info 
JOIN 
    users ON users.user_id = links_info.user 
WHERE 
    users.email = '$email';
")->fetch(PDO::FETCH_ASSOC);

        $info['total_files'] = $res['total_files'];
        $info['total_size'] = $res['total_size'];
        $info['downloads'] = $res['downloads'];
        $info['views'] = $res['views'];
        $info['broken'] = $res['broken'];
    }
    return $info;

}

function formatBytes($bytes, $precision = 2)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    // Uncomment one of the following alternatives
    $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . " " . $units[$pow];
}
function AES($action, $string)
{
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'myencrypt';
    $secret_iv = 'encyptaes';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function fetchFilesFromFolder($folderId, $apiKey)
{
    // Corrected indentation for better readability
    $apiUrl = "https://www.googleapis.com/drive/v3/files?q=%27{$folderId}%27+in+parents&fields=files(id,webViewLink,mimeType)&key={$apiKey}&orderBy=name&supportsAllDrives=True&includeItemsFromAllDrives=True";
    $response = fetchContent($apiUrl);
    if ($response === false) {
        return false;
    }
    $data = json_decode($response, true);
    $files = [];
    if (isset($data['files']) && count($data['files']) > 0) {
        foreach ($data['files'] as $file) {
            if ($file['mimeType'] != 'application/vnd.google-apps.folder') {
                // Modified array structure to match the desired format
                $files[] = [
                    'webViewLink' => $file['webViewLink']
                ];
            } else {
                $subfolderFiles = fetchFilesFromFolder($file['id'], $apiKey);
                if ($subfolderFiles !== false) {
                    $files = array_merge($files, $subfolderFiles);
                }
            }
        }
    }
    return $files;
}