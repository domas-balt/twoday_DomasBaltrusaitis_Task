<?php

namespace App;

require_once 'Autoloader.php';

use App\Caching\Cache;
use App\Logger\Handler\FileHandler;
use App\Logger\Logger;
use App\Services\FileService;
use App\Services\HyphenationService;
use App\Services\ResultVisualizationService;
use Memcached;

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

        /*
        'brew services start memcached'
         telnet localhost 11211
        */
        $memcached = new Memcached();
        $memcached->addServer("localhost", 11211);
        $cache = new Cache($memcached, $logger);

        $resultVisualizationService = new ResultVisualizationService($logger, $cache);

        echo "Enter the word you want to hyphenate:\n";
        $word = trim(fgets(STDIN));
        $word = strtolower($word);

        $resultVisualizationService->startAppLogger();
        $timerStart = hrtime(true);

        if ($cache->has($word)) {
            $resultVisualizationService->printString("Cached answer: " . $cache->get($word));
        } else {
            $syllableArray = FileService::readDataFromFile();

            $hyphenationService = new HyphenationService($word, $syllableArray);
            $hyphenationService->hyphenateWord();

            $finalHyphenatedWord = $hyphenationService->getFinalWord();

            $selectedSyllableArray = $hyphenationService->getSelectedSyllableArray();

            $resultVisualizationService->logSelectedSyllables($selectedSyllableArray);
            $resultVisualizationService->visualizeResults($finalHyphenatedWord, $word);
        }

        $timerEnd = hrtime(true);
        $resultVisualizationService->endAppLogger($timerStart, $timerEnd);
    }
}

$app = new Main();
$app->run();