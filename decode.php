<?php
require('functions.php');
$email = $_GET['email'];
setCookie('ddeml' , AES("encrypt", $email), time() + 8600*30, '/');
?>