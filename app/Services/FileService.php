<?php

namespace App\Services;

use Exception;

class FileService{
    public function ReadDataFromFile(): array
    {
        $fileName = "../Files/hyphen.txt";

        if(!file_exists($fileName) || !is_readable($fileName)){
            throw new Exception("File does not exist or is not readable");
        }

        $file = fopen($fileName, "r");
        if($file){
            $hyphenArray = explode("\n", fread($file, filesize($fileName)));
        }

        fclose($file);

        if (!empty($hyphenArray)) {
            return $hyphenArray;
        }

        return [];
    }
}

$app = new FileService();
$app->ReadDataFromFile();