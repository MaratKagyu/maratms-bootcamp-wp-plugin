<?php

// Enables class auto-loading for MaratMSBootcampPlugin namespace (root = ./includes)

spl_autoload_register(function ($class) {
    if (preg_match('#^MaratMSBootcampPlugin\\\\(.*)#', $class, $pregResult)) {
        $file = __DIR__ . "/includes/" . str_replace('\\', '/', $pregResult[1]) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});
