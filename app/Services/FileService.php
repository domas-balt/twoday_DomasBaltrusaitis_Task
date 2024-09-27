<?php
declare(strict_types=1);

namespace App\Services;

use Exception;

class FileService
{
    public static function readDataFromFile(string $fileName): array
    {
        $fullPath = dirname(__DIR__) . $fileName;

        if (file_exists($fullPath) && is_readable($fullPath)) {
            return file($fullPath);
        }

        throw new Exception("File not found or not readable");
    }

    public static function printDataToFile(string $fileName, array $content): void
    {
        $fullPath = dirname(__DIR__) . $fileName;
        file_put_contents($fullPath, $content);
    }
}