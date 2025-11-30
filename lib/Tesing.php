<?php

include_once("../config.php");
include_once("../db.php");
require_once('../autoload.php');

// header("Content-type: Application/json");



$remoteUrl = "https://snowy-river-337d.bigila1739.workers.dev/1-zxP7ERaBi1AARIQ7K0ZcTHgoLkrJQpf/Wolf%20King%20S01E07%20720p%20HEVC%2010bit%20NF%20WEB-DL%20Dual%20Audio%20ESub%20[DeadToon.mkv";

// NEODRIVE

Neodrive::checkApi("53e0b0e686f4f107d1b0c54d58754a4d","deadtoons06@gmail.com");
// print_r(Neodrive::checkApi("53e0b0e686f4f107d1b0c54d58754a4d","deadtoons06@gmail.com"));


// UpnShare


// $upn = new UpnShare("https://upnshare.com","583281b2bbec489a5597b4b9");
// print_r($upn->checkApi());
// print_r($upn->upload("https://snowy-river-337d.bigila1739.workers.dev/10JU3NxWs5NwlKTxzl-e3VvfxiJYkbyZM/BollyMod.Top%20-%20Peaky.Blinders.S01E06.1080p.BluRay.HIN-ENG.x264.ESub.mkv","test2.mkv"));
// print_r($upn->getUploadStatus("lvahi"));
// print_r($upn->getPlayStatus("x6nqh8"));
// print_r($upn->);


// RpmShare


// $rpm = new RpmShare(RPMSHARE_API_URL,"0167c19bab003726abff5beb");

// print_r($rpm->checkApi());

// print_r($rpm->upload("https://snowy-river-337d.bigila1739.workers.dev/1vKgRKFUdt7L7Ov3jvI9Da9gB0d8myl0E/Peaky%20Blinders%20S02E05%201080p%2010bit%20BluRay%20HIN%20ENG%20ESub%20[DeadToons].mkv","test2.mkv"));

// print_r($rpm->getUploadStatus("wkwy8"));
// print_r($rpm->getPlayStatus("6kjdt"));

// print_r($rpm->);


// GoogleDrive

// print_r(GoogleDrive::fetchFileInfo("17iS38rQzPP2xFSOaCuhMC92Rt-0afqmX"));
// print_r(GoogleDrive::fetchFilesFromFolder("1AKQW2dn32zImk3WlrPadbOnaHCYW3wfB"));

// Queue



// $queue = new Queue($pdo);
// echo $link_id = $queue->getQueue();
// $queue->setLinkId(74341);
// $queue->setup();

// $queue->setUid("a435e");
// print_r($queue->processAllServers());
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
// $queue->uploadNeoDrive("53e0b0e686f4f107d1b0c54d58754a4d","deadtoons06@gmail.com");

// $queue->processQueue();

// echo json_encode($queue->getlogs());


// $queue->processQueue();

// exit;


// User

// $user = new User($pdo);
// $user->setUserId(2);
// $user->setUserEmail("zikeverywhere@gmail.com");
// // echo $user->getUserId();
// // $user->setUserWebsite("ZikAnime","zikanime.xyz");

// // print_r($user->info());
// // print_r($user->getUserServerDetails(1));
// // print_r($user->moveServer(16,"up"));
// print_r($user->getUserSettings());
// print_r($user->UserApis('zip'));

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

// print_r(Hubcloud::upload("ekN2aFdQbSs5dGpYN0RPNVhmNUVCdz09","10I8XteB4rKpmn1UNu11WuHu1_FB2hoXW"));
// print_r(Hubcloud::checkApi("ekN2aFdQbSs5dGpYN0RPNVhmNUVCdz09"));

// Filepress

// print_r(Filepress::upload("/1Y9tekNZgjl7FUjHPBPfeg596RgDqtrCnbkrBPqBWY=","1-ySNUrWo3HtB64wKzTpkkRvgtbQvqInG"));

// SendCm

// print_r(Send::checkApi("246002h0ho8jnt1tat1w3"));
// print_r(Send::upload("246002h0ho8jnt1tat1w3","https://snowy-river-337d.bigila1739.workers.dev/1-ySNUrWo3HtB64wKzTpkkRvgtbQvqInG/Wolf%20King%20S01E06%20720p%20HEVC%2010bit%20NF%20WEB-DL%20Dual%20Audio%20ESub%20[DeadToons].mkv"));
// print_r(Send::isWorking("246002h0ho8jnt1tat1w3f","yyakdxkd85pp"));
// exit;


// File

// $file = new File($pdo);
// $file->setLinkId(84785);
// echo $file->getByTemId("c593dd663b954810257c498a8fcb0005");
// print_r($file->getServers());

// echo $file->isFileDownloadable();
// if(!$file->checkServer(1)) {
//     echo "Not Available";
// }

// print_r($file->getDownloadServers());
// print_r($file->getWatchServers());
// $file->increaseDownload();

// echo $file->isZip();

// exit;


// print_r(GoogleDrive::fetchFileInfo("13mpR760NPeYiwH3MGIbkO456Rk6sT-w7"));


// echo StaticClass::isFileDownloadable("https://snowy-river-337d.bigila1739.workers.dev/1GL7RlCXJH1ihWUFTUxqTu2TpjqprTHcl/Jojo's%20Bizarre%20Adventure%20S02E13%201080p%20BDRip%20[Hindi-Eng-Jap]%20Esub%20[DeadToons].mkv");
   
    
