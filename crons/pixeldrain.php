<?php

function download() {

    $directUrl = "https://snowy-river-337d.bigila1739.workers.dev/10O0U68iXH9mSNJB8T9vSogu69HF16gfK/[DeadToons]%20Death%20Note%20S01%20[E01-12]%20Hindi%20Dubbed%20720p.zip";
    $savePath = "downloads/[DeadToons] Death Note S01 [E01-12] Hindi Dubbed 720p.zip";
    
    
    $fp = fopen($savePath, 'w');
    if (!$fp) {
        die("Failed to open file for writing.");
    }
    
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $directUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false); // Do not store in memory
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_FILE, $fp); // Write directly to file
    
    
    $success = curl_exec($ch);
    if ($success === false) {
        die("Download failed: " . curl_error($ch));
    }
    
    
    curl_close($ch);
    fclose($fp);
    
    echo "Download complete: $savePath";
    
}

function uploadToPixeldrain($filePath, $fileName, $apiKey) {
    $url = "https://pixeldrain.com/api/file/" . urlencode($fileName);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Basic " . base64_encode(":" . $apiKey)
    ]);

    $fileHandle = fopen($filePath, "r");
    curl_setopt($ch, CURLOPT_INFILE, $fileHandle);
    curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filePath));

    $response = curl_exec($ch);
    fclose($fileHandle);

    if (curl_errno($ch)) {
        echo "Error: " . curl_error($ch);
    } else {
        echo "Response: " . $response;
    }

    curl_close($ch);
}

$apiKey = "5d4de6c5-513c-4798-9d71-e3bb34a05d55";
$filePath = "downloads/file.mkv";
$fileName = "your_file.mkv";

// uploadToPixeldrain($filePath, $fileName, $apiKey);
download();

?>
