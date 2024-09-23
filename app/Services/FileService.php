<?php

namespace App\Services;

use Exception;

class FileService{
    private const string fileName = "/Files/hyphen.txt";
    public static function ReadDataFromFile(): array
    {
        $fileName = dirname(__DIR__, 1) . self::fileName;

        if(file_exists($fileName) && is_readable($fileName)){
            return file($fileName);
        }

        throw new Exception("File not found or not readable");
    }
}