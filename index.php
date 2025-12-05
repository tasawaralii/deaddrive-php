<?php

require_once './bootstrap.php';
require('functions.php');
require('db.php');
require("autoload.php");
require_once './template/template.php';

if (isset($_COOKIE['ddeml'])) {
    $email = AES('decrypt', $_COOKIE['ddeml']);
    $user = userinfo($email, $pdo, true);
    $_SERVER['user'] = $user;
}

$protected_paths = ["dashboard","share","files","account","broken-files","setting","status","videos"];

$path = $_SERVER['REQUEST_URI'];
$title = $_ENV['NAME'];
$filePath = __DIR__ . "/pages/404.php";

if ($path == "/") {
    $filePath = __DIR__ . "/pages/home.php";
} elseif (preg_match('#/(dashboard|share|files|login|about-us|account|broken-files|contact-us|copyright-policy|logout|privacy-policy|setting|status|terms-conditions|videos)#', $path, $matches)) {
    if(in_array($matches[1], $protected_paths)) {
        if(!isset($_SERVER['user'])) {
            $filePath = __DIR__ . "/pages/logout.php";
        }
    } else {
        $filePath = __DIR__ . "/pages/" . $matches[1] . ".php";
    }
}

if (file_exists($filePath)) {
    ob_start();

    require_once $filePath;

    $content = ob_get_clean();

    dd_render($title, $content);
    exit;

}

echo "404 " . $filePath;