<?php

namespace App;

require_once 'Autoloader.php';

use App\Logger\Handler\FileHandler;
use App\Logger\Logger;
use App\Logger\LogLevel;
use App\Services\FileService;
use App\Services\HyphenationService;
use App\Services\ResultVisualizationService;

class Main
{
    public function run(): void
    {
        spl_autoload_register("\\App\\Autoloader::CustomAutoloader");
        date_default_timezone_set("Europe/Vilnius");

        $logFileName = "app_log.txt";
        $handler = new FileHandler($logFileName);
        $logger = new Logger($handler);

        $logger->log(LogLevel::INFO, "RUNNING APP");

//        echo "Enter the word you want to hyphenate:\n";
//        $word = trim(fgets(STDIN));
//        $word = strtolower($word);
        $word = "generator";
        $timerStart = hrtime(true);
        $syllableArray = FileService::readDataFromFile();

        $hyphenationService = new HyphenationService($word, $syllableArray);
        $hyphenationService->hyphenateWord($logger);

        $finalHyphenatedWord = $hyphenationService->getFinalWord();

        $selectedSyllableArray = $hyphenationService->getSelectedSyllableArray();
        $resultVisualizationService = new ResultVisualizationService($logger);
        $resultVisualizationService->logSelectedSyllables($selectedSyllableArray);
        $resultVisualizationService->visualizeResults($finalHyphenatedWord);

        $timerEnd = hrtime(true);

        echo "The process took: " . ($timerEnd - $timerStart) / 1000000 . "ms.\n";
        $logger->log(LogLevel::INFO, "The process took: " . ($timerEnd - $timerStart) / 1000000 . "ms.");
        $logger->log(LogLevel::INFO, "STOPPING APP");
    }
}

$app = new Main();
$app->run();