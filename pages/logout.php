<?php
setcookie('ddeml', '', time() - (86400 * 30), '/');
header('location: /');
exit();