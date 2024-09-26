<?php

namespace App;

require_once 'Autoloader.php';

use App\Caching\Cache;
use App\Logger\Handler\FileHandler;
use App\Logger\Logger;
use App\Services\FileService;
use App\Services\HyphenationService;
use App\Services\ParagraphHyphenationService;
use App\Services\RegexHyphenationService;
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

//        $paragraphLineArray = FileService::readParagraphsFromFile();

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

//        echo "Enter the word you want to hyphenate:\n";
//        $word = trim(fgets(STDIN));
//        $word = strtolower($word);
        $word = "Mistranslate";

        $resultVisualizationService->startAppLogger();
        $timerStart = hrtime(true);

        $syllableArray = FileService::readDataFromFile( "/Files/hyphen.txt");
        $wordArray = FileService::readDataFromFile( "/Files/words.txt");
        $regexHyphenationService = new RegexHyphenationService($syllableArray, $wordArray);
        print_r($regexHyphenationService->hyphenateWord());
        $hyphenationService = new HyphenationService( "", $syllableArray);

//        $paragraphHyphenationService = new ParagraphHyphenationService($paragraphLineArray, $syllableArray, $resultVisualizationService);
//        echo implode($paragraphHyphenationService->hyphenateParagraph()) . "\n";


//        if ($cache->has($word)) {
//            $resultVisualizationService->printString("Cached answer: " . $cache->get($word));
//        } else {
//            $hyphenationService = new HyphenationService($word, $syllableArray);
//            $hyphenationService->hyphenateWord();
//
//            $finalHyphenatedWord = $hyphenationService->getFinalWord();
//
//            $selectedSyllableArray = $hyphenationService->getSelectedSyllableArray();
//
//            $resultVisualizationService->logSelectedSyllables($selectedSyllableArray);
//            $resultVisualizationService->visualizeResults($finalHyphenatedWord, $word);
//        }

//        $regexHyphenationServices = new RegexHyphenationService($syllableArray, $word);
//        $regexHyphenationServices->HyphenateWord();

        $timerEnd = hrtime(true);
        $resultVisualizationService->endAppLogger($timerStart, $timerEnd);
    }
}

$app = new Main();
$app->run();