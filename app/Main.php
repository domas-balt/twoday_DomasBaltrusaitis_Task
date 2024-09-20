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
        //$word = trim(fgets(STDIN));
        //$word = strtolower($word);

        $timerStart = hrtime(true);
        $word = "smart";
        $hyphenArray = FileService::ReadDataFromFile();

        $hyphenationService = new HyphenationService($word, $hyphenArray);
        $hyphenationService->FindSyllables();
        $timerEnd = hrtime(true);

        echo "The process took: " . ($timerEnd - $timerStart) / 1000000 . "ms.\n";
    }
}

$app = new Main();
$app->run();