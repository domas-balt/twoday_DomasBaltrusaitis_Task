<?php

namespace App\Services;

use Exception;

class FileService
{
    private const string FILE_NAME = "/Files/hyphen.txt";
    public static function readDataFromFile(): array
    {
        $fileName = dirname(__DIR__, 1) . self::FILE_NAME;

        if (file_exists($fileName) && is_readable($fileName)) {
            return file($fileName);
        }

        throw new Exception("File not found or not readable");
    }
}