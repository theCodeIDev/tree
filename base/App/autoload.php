<?php

function autoloadDir($className) {
    $path = dirname(__FILE__);
    scanDirectory($path,$className);
}

function scanDirectory($directory,$className){

    $files = scandir($directory);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $path = $directory . '/' . $file;

        if (is_dir($path)) {
            scanDirectory($path,$className);
        } else {

            if( $file == $className.'.php' ){
                require_once($path);
                return true;
            }

        }
    }
}

spl_autoload_register('autoloadDir');
