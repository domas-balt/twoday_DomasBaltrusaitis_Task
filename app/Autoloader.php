<?php

namespace App;

class Autoloader {
    protected static string $fileExtension = '.php';
    protected static string $filePath = __DIR__;

    public static function CustomAutoloader($className) : void
    {
        $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
        $className = ltrim($className, "App\\");
        $file = self::$filePath . DIRECTORY_SEPARATOR . $className . self::$fileExtension;

        if (file_exists($file)){
            require_once($file);
        }
    }

    public static function setFileExtension(string $fileExtension) : void
    {
        self::$fileExtension = $fileExtension;
    }

    public static function setFilePath(string $rootPath) : void
    {
        self::$filePath = $rootPath;
    }
}