<?php

require('db.php');

if(!isset($_GET['uid'])) {
    // header("Location: https://google.com");
}

$uid = $_GET['uid'];

$isValidUid = $pdo->query("SELECT 1 FROM links_info WHERE uid = '$uid'")->fetchColumn();

if(!$isValidUid) {
    header("Location: https://google.com");
}
   
$temLinkId = $pdo->query("SELECT TempLinkId FROM templinks WHERE uid = '$uid'")->fetchColumn();

if(!$temLinkId) {
    
    $temLinkId = bin2hex(random_bytes(16));
    $expirationTime = date('Y-m-d H:i:s', strtotime('+4 hours'));
    
    $stmt = $pdo->prepare("INSERT INTO templinks (Uid, TempLinkId, ExpirationTime) VALUES (?, ?, ?)");
    $stmt->execute([$uid, $temLinkId, $expirationTime]);
    
}

// header("Location: https://raspy-lab-097e.hemepa6025.workers.dev/?id=$temLinkId");
header("Location: https://deaddrive.icu/catch-me-watching.php?temid=$temLinkId");
exit();

?>
