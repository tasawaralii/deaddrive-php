<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == "rename" && isset($_POST['rename']) && isset($_POST['id'])) {
        $rename = $_POST['rename'];
        $id = $_POST['id'];
        try {
            $sql = "UPDATE links_info SET Name = :rename WHERE uid = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':rename', $rename, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $success = $stmt->execute();
            if ($success) {
                header("Location: $deaddrive/files");
                exit;
            } else {
                echo "Error updating Name.";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }

}