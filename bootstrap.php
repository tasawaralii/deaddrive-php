<?php
if (file_exists(__DIR__ . '/env.php')) {
    require_once __DIR__ . '/env.php';
}

spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/lib/';

    $file = $base_dir . $class . '.php';

    if (file_exists($file)) {
        require $file;
    }
});