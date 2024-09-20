<?php

namespace App;

require_once 'Services/FileService.php';
require_once 'Services/HyphenationService.php';

use App\Services\FileService;
use App\Services\HyphenationService;

class Main{

    public function run(): void
    {
        echo "Enter the word you want to hyphenate:\n";
        $word = trim(fgets(STDIN));
        $hyphenArray = FileService::ReadDataFromFile();

        $hyphenationService = new HyphenationService($word, $hyphenArray);
        $hyphenationService->FindSyllables();
    }
}

$app = new Main();
$app->run();