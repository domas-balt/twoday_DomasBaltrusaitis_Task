<?php

namespace App\Services;

use Exception;

class FileService
{
    private const string FILE_NAME = "/Files/hyphen.txt";
    private const string PARAGRAPH_FILE_NAME = "/Files/paragraph.txt";

    public static function readDataFromFile($fileName): array
    {
        $fullPath = dirname(__DIR__) . $fileName;

        if (file_exists($fullPath) && is_readable($fullPath)) {
            return file($fullPath);
        }

        throw new Exception("File not found or not readable");
    }
}