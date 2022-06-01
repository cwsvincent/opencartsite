<?php

spl_autoload_register(function($class)
{
    $file = DIR_DOMPDF.'/../lib/'.strtr($class, '\\', '/').'.php';
    if (file_exists($file)) {
        require $file;
        return true;
    }
});