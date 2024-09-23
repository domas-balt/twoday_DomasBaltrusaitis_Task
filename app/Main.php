<?php

namespace App;

require_once 'Autoloader.php';

use App\Services\FileService;
use App\Services\HyphenationService;
use App\Services\ResultVisualizationService;

class Main{

    public function run(): void
    {
        echo "Enter the word you want to hyphenate:\n";
        $word = trim(fgets(STDIN));
        $word = strtolower($word);

        spl_autoload_register("\\App\\Autoloader::CustomAutoloader");

        $timerStart = hrtime(true);
        $syllableArray = FileService::readDataFromFile();

        $hyphenationService = new HyphenationService($word, $syllableArray);
        $hyphenationService->hyphenateWord();

        $finalHyphenatedWord = $hyphenationService->getFinalWord();

        ResultVisualizationService::visualizeResults($finalHyphenatedWord);

        $timerEnd = hrtime(true);

        echo "The process took: " . ($timerEnd - $timerStart) / 1000000 . "ms.\n";
    }
}

$app = new Main();
$app->run();