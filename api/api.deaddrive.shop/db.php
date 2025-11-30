<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'deaddrive';
$db_user = 'deaddrive';
$db_pass = '6@7A8a9a';

// Establish database connection
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $pdo->exec("SET NAMES 'utf8mb4'");

    
    // Set default fetch mode to FETCH_ASSOC
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
