<?php

namespace App;

class Autoloader {
    public static function CustomAutoloader($className) : void{
        $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
        $className = ltrim($className, "App\\");
        $file = __DIR__ . DIRECTORY_SEPARATOR . $className . '.php';

        if (file_exists($file)){
            require_once($file);
        }
    }
}