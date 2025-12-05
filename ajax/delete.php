<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["action"]) && $_POST["action"] == "delete" && isset($_POST["id"]) && is_array($_POST["id"])) {
        $stmt = $pdo->prepare("DELETE FROM links_info WHERE uid = :id");
        foreach ($_POST["id"] as $id) {
            $stmt->bindParam(":id", $id);
            $res = $stmt->execute();
        }
        header("Location: $deaddrive/files");
        exit;
    }

}