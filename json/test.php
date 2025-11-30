<?PHP
$conn = new mysqli('localhost', 'fulltoon_anime', '6@7A8a9a', 'fulltoon_Anime');
$uid = $_GET['id'];
$sql = "SELECT users.site_url, users.site_name , Links_info.* , Servers.* FROM Links_info JOIN Servers ON Links_info.Id = Servers.Id Join users on Links_info.user = users.user_id WHERE Servers.uid ='$uid'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
if($row['deleted'] == 1) {
    $array = array('status' => 'deleted', 'id' => 'unknown', 'name' => 'This File Is Deleted' ,'message' => 'The User Has Deleted This File');
    $json = json_encode($array);
echo $json;
exit;
}
// print_r($row);
if (!empty($row)) {
    $id = $row['Id'];
    $servers = ['download' => [],'watch' => []]; // Initializing arrays for watch and download servers
    $found_live = false;
    
    foreach ($row as $key => $value) {
        if ($key === 'hubcloud') {
            $found_live = true;
            continue;
        }
        
        if ($found_live && $value !== '' && $value !== null && $key !== 'uid' && $key != 'user') {
            $server = ['id' => $value];
            
            $info = "SELECT * FROM Server_info WHERE Name = '$key'";
            $infoResult = mysqli_query($conn, $info);
            $infoRow = mysqli_fetch_assoc($infoResult);
            
            foreach ($infoRow as $subkey => $subvalue) {
                $server[$subkey] = $subvalue;
            }
            
            if ($server['watch'] === '1') {
                $servers['watch'][] = $server; // Adding server to watch array
            }
            
            if ($server['down'] === '1') {
                $servers['download'][] = $server; // Adding server to download array
            }
        }
    }

    $array = [
        'status' => 'true',
        'user' => $row['user'],
        'site_name' => $row['site_name'],
        'site' => $row['site_url'],
        'id' => $id,
        'name' => $row['Name'],
        'size' => $row['size'],
        'extension' => $row['Type'],
        'mimeType' => $row['mimeType'],
        'duration' => $row['duration'],
        'date' => $row['new_date'],
        'live' => $row['live'],
        'servers' => $servers // Include the modified servers array
    ];
    
    $json = json_encode($array, JSON_PRETTY_PRINT);
    
    $count = "UPDATE Links_info SET views = views+1 WHERE Id = '$id'";
    mysqli_query($conn, $count);
}
else {
$array = array('status' => 'false', 'id' => 'unkmown', 'name' => '404 Not Found' ,'message' => 'Just Download File And Do not Try to become Over Smart');
$json = json_encode($array);
    }
echo $json;
exit;
?>