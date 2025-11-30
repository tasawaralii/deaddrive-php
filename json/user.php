<?php
require_once('../db.php');
$email = $_GET['email'];
$user_sql = "SELECT * FROM users WHERE email = '$email'";
$user_statement = $pdo->query($user_sql);
$user = $user_statement->fetch(PDO::FETCH_ASSOC);
$user_id = $user['user_id'];
// Fetch additional user info from Links_info table
$info_sql = "SELECT 
    COUNT(*) AS total_files, 
    SUM(size) AS total_size, 
    SUM(views) AS downloads, 
    SUM(CASE WHEN Servers.live = 0 THEN 1 ELSE 0 END) AS broken,
    COUNT(CASE WHEN deleted = 1 THEN 1 END) AS deleted_files
FROM 
    Links_info 
JOIN 
    Servers ON Links_info.Id = Servers.Id 
WHERE 
    Links_info.user = '$user_id'";
$info_statement = $pdo->query($info_sql);
$user_info = $info_statement->fetch(PDO::FETCH_ASSOC);


$sql = $pdo->query("SELECT api.*
FROM api
JOIN users ON users.user_id = api.user
WHERE users.email IN ('$email' , 'deaddrived@gmail.com') ORDER BY users.user_id ASC;
");
$res = $sql->fetchAll(PDO::FETCH_ASSOC);
$api = [];
if (count($res) == 1) {
    $api = $res[0];
} else {
    $user_api = $res[1];
    foreach ($user_api as $key => $uapi) {
        $api["$key"] = ($uapi == '' || $uapi == null) ? $res[0]["$key"] : $uapi;
    }
}

// print_r($res);
$result = array_merge($user, $user_info, ['api' => $api]);
print_r(json_encode($result));
?>