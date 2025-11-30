<?php
require_once("../db.php");
require_once("../config.php");
require_once("../autoload.php");


$links = [];

$packs = new Pack($pdo);


$isPost = $_SERVER['REQUEST_METHOD'] == "POST";

if(!$isPost) {

    $links = $packs->notAppdrive();
}

if($isPost) {
    // header("Content-type: Application/json");


    $appdriveString = $_POST['appdrive_links'];

    $pattern = '/<a href="([^"]+)">(.+?)<\/a>/i';

    preg_match_all($pattern, $appdriveString, $matches);

    // print_r($matches);
    
    $uid_appdrive = [];
    
    foreach($matches[2] as $index => $appdrive) {
        
        $name = preg_replace('/ \[\d+(\.\d+)? [A-Z]{2,3}\]$/', '', $appdrive);
        
        preg_match('/https:\/\/[a-z0-9.]+\/file\/([a-zA-Z0-9]+)/', $matches[1][$index], $slug_matches);
        $appdrive_slug = $slug_matches[1] ?? null;
        
        $stmt = $pdo->prepare("SELECT link_id,uid FROM links_info WHERE Name = :name AND user = 2");
        $stmt->execute([":name" => $name]);
        $link_info = $stmt->fetch();
        
        if(!$link_info) {
            echo "Name not Matched for " . $name . "<br>";
            continue;
        }
        
        $uid_appdrive = ['appdrive' => $appdrive_slug,'name' => $name, 'link_info' => $link_info];
        
        $file = new File($pdo);
        $file->setLinkId($link_info['link_id']);
        
        if($file->checkServer(3)) {
            echo "Already Present in <a href='https://deaddrive.shop/file/".$link_info['uid'] . "'>" . $name . "</a><hr>";
            continue;
        }
        
        $file->storeServer(3,$appdrive_slug,"b081ff78b9fe1b375abc103bfd5dc3fa");
        
        echo "<a href='https://deaddrive.shop/file/".$link_info['uid'] . "'>" . $name . "</a><hr>";

        // print_r(json_encode($uid_appdrive));
    }

    
}

if($links) {
    
?>

<textarea id="drive_urls" cols="100" rows="20" readonly>
<?php
    foreach ($links as $link) {
        echo $link['drive_link'] . PHP_EOL;
    }
?>
</textarea>

<hr>

<form method="post">

<textarea name="appdrive_links" cols="100" rows = "20"/>
</textarea>
<button type="submit">Submit</button>

</form>

<script>
    document.getElementById("drive_urls").addEventListener("click", copy);
    function copy() {
        var links = document.getElementById("drive_urls");
        
        links.select();
        links.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(links.value)
            .then(() => alert("Copied to clipboard!"))
            .catch(err => console.error("Error copying:", err));
        
    }
</script>

<?php
}
