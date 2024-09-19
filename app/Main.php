<?php

namespace App;

require_once 'Services/FileService.php';

use App\Services\FileService;

class Main{

    public function run(): void
    {
        echo "Enter the word you want to hyphenate:\n";
        $word = trim(fgets(STDIN));

        $hyphenArray = FileService::ReadDataFromFile();
        //print_r($hyphenArray);
    }
}

$app = new Main();
$app->run();