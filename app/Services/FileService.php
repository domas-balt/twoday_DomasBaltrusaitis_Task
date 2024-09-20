<?php

namespace App\Services;

use Exception;

class FileService{
    public static function ReadDataFromFile(): array
    {
        $fileName = dirname(__DIR__, 1) . "/Files/hyphen.txt";

        if(file_exists($fileName) && is_readable($fileName)){
            return file($fileName);
        }

        throw new Exception("File not found or not readable");
    }
}