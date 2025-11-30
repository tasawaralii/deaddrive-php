<?php

require_once './bootstrap.php';
require('functions.php');
require('db.php');
require_once("config.php");
require("autoload.php");

$path = $_SERVER['REQUEST_URI'];

if($path = "/") {
    require_once __DIR__ . "/page/home.php";
}

$route = trim($path,'/');

$filePath = __DIR__ . '/page/' . $route . '.php';

if(file_exists($filePath)) {
    
    require_once $filePath;
    
    exit;
}

echo "404 " . $filePath;