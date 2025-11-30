<?php
require('../db.php');
require('../site_info.php');
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
    
    if (isset($_POST['action']) && $_POST['action'] == "restore" && isset($_POST['id'])) {
        $id = $_POST['id'];
        try {
            $sql = "UPDATE links_info 
SET deleted = 0
WHERE Id IN (SELECT Id FROM Servers WHERE uid = :id)
";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $success = $stmt->execute();
            if ($success) {
               header("Location: $deadrive/trash");
               exit;
            } else {
                echo "Error Restoring File.";
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
    }
    
    if (isset($_POST["action"]) && $_POST["action"] == "delete" && isset($_POST["id"]) && is_array($_POST["id"])) {
                $stmt = $pdo->prepare("DELETE FROM links_info WHERE uid = :id");
            foreach ($_POST["id"] as $id) {
                $stmt->bindParam(":id", $id);
                $res = $stmt->execute();
            }
            header("Location: $deaddrive/files");
            exit;
    }
    
    if (isset($_POST["action"]) && $_POST["action"] == "RestoreSelected" && isset($_POST["id"]) && is_array($_POST["id"])) {
        try {
                $stmt = $pdo->prepare("UPDATE links_info 
JOIN Servers ON links_info.Id = Servers.Id 
SET links_info.deleted = 0 
WHERE Servers.uid = :id;
");
            foreach ($_POST["id"] as $id) {
                $stmt->bindParam(":id", $id);
                $res = $stmt->execute();
            }
            header("Location: $deadrive/trash");
            exit;
        } catch (PDOException $e) {
            // Error message
            echo "Error: " . $e->getMessage();
        }

        // Close the database connection
        $pdo = null;
    }
    
}
?>