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
        $loader = new Autoloader();
        $loader->register();
        $loader->addNamespace('App', __DIR__);

        date_default_timezone_set("Europe/Vilnius");

        $logFileName = "app_log.txt";

        $handler = new FileHandler($logFileName);
        $logger = new Logger($handler);
        $resultVisualizationService = new ResultVisualizationService($logger);

        // TURNED OFF FOR DEBUGGING
//        echo "Enter the word you want to hyphenate:\n";
//        $word = trim(fgets(STDIN));
//        $word = strtolower($word);
        $word = "generator";

        $resultVisualizationService->startAppLogger();
        $timerStart = hrtime(true);

        $syllableArray = FileService::readDataFromFile();

        $hyphenationService = new HyphenationService($word, $syllableArray);
        $hyphenationService->hyphenateWord($logger);

        $finalHyphenatedWord = $hyphenationService->getFinalWord();

        $selectedSyllableArray = $hyphenationService->getSelectedSyllableArray();

        $resultVisualizationService->logSelectedSyllables($selectedSyllableArray);
        $resultVisualizationService->visualizeResults($finalHyphenatedWord);

        $timerEnd = hrtime(true);

        $resultVisualizationService->endAppLogger($timerStart, $timerEnd);
    }
}

$app = new Main();
$app->run();