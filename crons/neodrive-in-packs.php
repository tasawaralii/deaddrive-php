<?php
require_once("../db.php");
require_once("../config.php");
require_once("../autoload.php");

$packs = new Pack($pdo);
$packs->notNeodrive();
