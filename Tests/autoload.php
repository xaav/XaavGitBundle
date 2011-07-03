<?php


spl_autoload_register(function($class)
{
    $file = __DIR__.'/../'.strtr(substr($class, 15), '\\', '/').'.php';
    if (file_exists($file)) {
        require $file;
        return true;
    }

    echo $file;
});

