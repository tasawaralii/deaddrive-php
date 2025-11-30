<?php

require("autoload.php");
require("db.php");
require("config.php");

header("content-type: application/json");

if(DEVELOPMENT_MODE) {
    StaticClass::dieError('Sharing is temporarily Disabled Due to Maintaining');
}


$apiKey = $_GET['api'] ?? StaticClass::dieError('No API provided');
$driveId = $_GET['drive_id'] ?? StaticClass::dieError('No Google Drive File ID provided');

$user = User::validateUserApi($apiKey,$pdo);

if(!$user) {
    StaticClass::dieError('Wrong Api');
}

$User = new User($pdo);
$User->setUserApi($apiKey);

if(!$User->userWebsite()) {
    StaticClass::dieError('Set Your Website Link In Account Page');
}

$fileInfo = GoogleDrive::fetchFileInfo($driveId);

if($fileInfo['status'] == "error") {
    StaticClass::dieError($fileInfo['message']['error']['message']);
}

$fileInfo = $fileInfo['message'];

$file = File::addFile($fileInfo,$user['user_id'],$pdo);

$response = [
    'fileStatus' => $file['status'],
    'key' => $file['uid'],
    'name' => $fileInfo['name'],
    'driveId' => $fileInfo['id'],
    'size' => $fileInfo['size'],
    'download' => DEADDRIVE_DOMAIN . '/file/' . $file['uid'],
    'watch' => DEADDRIVE_DOMAIN . '/embed/' . $file['uid']
];


$newFile = new File($pdo);

$newFile->setLinkId($file['link_id']);
$newFile->addInQueue();

StaticClass::dieSuccess($response);