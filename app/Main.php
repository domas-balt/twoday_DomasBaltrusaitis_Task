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
        $word = strtolower($word);

        $word = "discombobulated";
        $timerStart = hrtime(true);
        $hyphenArray = FileService::ReadDataFromFile();

        $hyphenationService = new HyphenationService($word, $hyphenArray);
        $hyphenationService->FindSyllables();

        $finalHyphenatedWord = $hyphenationService->GetFinalWord();
        $timerEnd = hrtime(true);

        echo "The process took: " . ($timerEnd - $timerStart) / 1000000 . "ms.\n";
    }
}

$app = new Main();
$app->run();