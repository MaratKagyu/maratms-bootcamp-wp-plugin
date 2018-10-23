<?php

// Enables class auto-loading for MaratMSBootcampPlugin namespace

spl_autoload_register(function ($class) {
    if (preg_match('#^MaratMSBootcampPlugin\\\\(.*)#', $class, $pregResult)) {
        $file = MARATMS_BP__PLUGIN_DIR . "includes/" . str_replace('\\', '/', $pregResult[1]) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});
