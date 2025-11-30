<?php
require('../db.php');
$pdo->query("DELETE FROM templinks WHERE ExpirationTime < NOW()");
?>