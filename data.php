<?PHP
require('json/site.php');
require('functions.php');
if(isset($_COOKIE['ddeml'])) {
$email = $_COOKIE['ddeml'];
$email = AES('decrypt', $email);
$user = $site['domain']."/json/user.php?email=".$email;
$user = json_decode(file_get_contents($user), true);
$userFname = trim($user['first_name'] . ' ' . $user['last_name']);
}