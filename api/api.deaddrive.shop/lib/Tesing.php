<?php

include_once("../config.php");
include_once("../db.php");
require_once('../autoload.php');

header("Content-type: Application/json");

// GoogleDrive

// print_r(GoogleDrive::fetchFileInfo("17iS38rQzPP2xFSOaCuhMC92Rt-0afqmX"));
// print_r(GoogleDrive::fetchFilesFromFolder("1AKQW2dn32zImk3WlrPadbOnaHCYW3wfB"));

// Queue



// $queue = new Queue($pdo);
// echo $link_id = $queue->getQueue();
// $queue->setLinkId(74341);
// $queue->setup();

// $queue->uploadPlayerx("xPwmVHqpF63oskrn");  // fully working with logs 03-22-2025
// $queue->uploadFilemoon("52927c8ux6g1v11pfyp7j"); // fixed fully working with logs 03-22-2025
// $queue->uploadStreamHG("15063uusz0xxi8emrr3cf"); // fixed fully working with logs 03-22-2025
// $queue->uploadEarnVids("28779o7i72p0vr3jx8les"); // fixed fully working with logs 03-22-2025
// $queue->uploadVoe("yr5hpJOdpHk2b4O7lggYwBLRtdGD9tM07nqQBUpzysGJZUsa4fONLsejrl5zU70N");
// $queue->uploadGdtot();
// $queue->uploadHydrax("83d8bbaa9e31453befc3a07dd43b1a40");
// $queue->uploadHubcloud("ekN2aFdQbSs5dGpYN0RPNVhmNUVCdz09");
// $queue->uploadFilepress("/1Y9tekNZgjl7FUjHPBPfeg596RgDqtrCnbkrBPqBWY=");
// $queue->uploadSend("246002h0ho8jnt1tat1w3f");

// $queue->processQueue();

// echo json_encode($queue->getlogs());


// $queue->processQueue();

// exit;


// User

// $user = new User($pdo);
// // $user->setUserId(2);
// $user->setUserEmail("zikeverywhere@gmail.com");
// // echo $user->getUserId();
// // $user->setUserWebsite("ZikAnime","zikanime.xyz");

// // print_r($user->info());
// // print_r($user->getUserServerDetails(1));
// // print_r($user->moveServer(16,"up"));
// print_r($user->getUserSettings());

// exit;


// Playerx : Fixed All Working 03-22-2025

// print_r(Playerx::upload("xPwmVHqpF63oskrn", "https://snowy-river-337d.bigila1739.workers.dev/1-ySNUrWo3HtB64wKzTpkkRvgtbQvqInG/Wolf%20King%20S01E06%20720p%20HEVC%2010bit%20NF%20WEB-DL%20Dual%20Audio%20ESub%20[DeadToons].mkv"));
// print_r(Playerx::checkStatus("2F8APlR5wSfo","xPwmVHqpF63oskrn"));
// print_r(Playerx::isWorking("c9GLWBESHBJS","xPwmVHqpF63oskrn"));
// exit;


// Filemoon : Fixed All Working 03-22-2025

// print_r(Filemoon::checkApi("52927c8ux6g1v11pfyp7j"));
// print_r(Filemoon::upload("52927c8ux6g1v11pfyp7j","https://snowy-river-337d.bigila1739.workers.dev/1-zxP7ERaBi1AARIQ7K0ZcTHgoLkrJQpf/Wolf%20King%20S01E07%20720p%20HEVC%2010bit%20NF%20WEB-DL%20Dual%20Audio%20ESub%20[DeadToons].mkv"));
// print_r(Filemoon::isWorking("52927c8ux6g1v11pfyp7j","fpgr3hgv59cr"));
// exit;



// StreamHG : Fixed All Working 03-22-2025

// print_r(StreamHG::checkApi("15063uusz0xxi8emrr3cf"));
// print_r(StreamHG::upload("15063uusz0xxi8emrr3cf","https://snowy-river-337d.bigila1739.workers.dev/1-ySNUrWo3HtB64wKzTpkkRvgtbQvqInG/Wolf%20King%20S01E06%20720p%20HEVC%2010bit%20NF%20WEB-DL%20Dual%20Audio%20ESub%20[DeadToons].mkv"));
// print_r(StreamHG::isWorking("15063uusz0xxi8emrr3cf","v36a6ysjt57o"));
// exit;


// Earnvids : Fixed All Working 03-22-2025

$remoteUrl = "https://snowy-river-337d.bigila1739.workers.dev/1-zxP7ERaBi1AARIQ7K0ZcTHgoLkrJQpf/Wolf%20King%20S01E07%20720p%20HEVC%2010bit%20NF%20WEB-DL%20Dual%20Audio%20ESub%20[DeadToon.mkv";
// print_r(EarnVids::checkApi("28779o7i72p0vr3jx8les"));
// print_r(EarnVids::upload("28779o7i72p0vr3jx8les",$remoteUrl));
// print_r(EarnVids::isWorking("28779o7i72p0vr3jx8les","x9oi5dwa2q9h"));
//     echo $earnvids->isWorking();
// exit;


// Voe : Fixed All Working 03-22-2025 (gives bad header on bad requests)


// print_r(Voe::checkApi("yr5hpJOdpHk2b4O7lggYwBLRtdGD9tM07nqQBUpzysGJZUsa4fONLsejrl5zU70N"));
// print_r(Voe::upload("yr5hpJOdpHk2b4O7lggYwBLRtdGD9tM07nqQBUpzysGJZUsa4fONLsejrl5zU70N", $remoteUrl));
// print_r(Voe::isWorking("yr5hpJOdpHk2b4O7lggYwBLRtdGD9tM07nqQBUpzysGJZUsa4fONLsejrl5zU70N","snvwvdis7xeq"));

// exit;


// Gdtot : Fixed All Working 03-22-2025 (gives bad header on bad requests)


// print_r(Gdtot::upload("1-ySNUrWo3HtB64wKzTpkkRvgtbQvqInG","fF4Lj0UWC8QREhhTNnxyU1zvEE5OP9","deaddrived@gmail.com"));


// Hydrax

// print_r(Hydrax::upload("83d8bbaa9e31453befc3a07dd43b1a40","1-ySNUrWo3HtB64wKzTpkkRvgtbQvqInG"));

// Hubcloud 

// print_r(Hubcloud::upload("ekN2aFdQbSs5dGpYN0RPNVhmNUVCdz0","1-ySNUrWo3HtB64wKzTpkkRvgtbQvqIn"));

// Filepress

// print_r(Filepress::upload("/1Y9tekNZgjl7FUjHPBPfeg596RgDqtrCnbkrBPqBWY=","1-ySNUrWo3HtB64wKzTpkkRvgtbQvqInG"));

// SendCm

// print_r(Send::checkApi("246002h0ho8jnt1tat1w3"));
// print_r(Send::upload("246002h0ho8jnt1tat1w3","https://snowy-river-337d.bigila1739.workers.dev/1-ySNUrWo3HtB64wKzTpkkRvgtbQvqInG/Wolf%20King%20S01E06%20720p%20HEVC%2010bit%20NF%20WEB-DL%20Dual%20Audio%20ESub%20[DeadToons].mkv"));
// print_r(Send::isWorking("246002h0ho8jnt1tat1w3f","yyakdxkd85pp"));
// exit;


// File

$file = new File($pdo);
// $file->setLinkId(74339);
echo $file->getByTemId("c593dd663b954810257c498a8fcb0005");
// print_r($file->getServers());

// echo $file->isFileDownloadable();
// if($file->checkServer(11)) {
//     echo "Errir";
// }

// print_r($file->getDownloadServers());
// print_r($file->getWatchServers());
$file->increaseDownload();

exit;


// print_r(GoogleDrive::fetchFileInfo("13mpR760NPeYiwH3MGIbkO456Rk6sT-w7"));


   
    
