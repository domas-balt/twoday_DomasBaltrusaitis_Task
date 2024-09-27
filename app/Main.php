<?php
declare(strict_types=1);

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
use App\Services\UserInputService;
use App\Utilities\Timer;
use Memcached;

class Main
{
    public function run(): void
    {
        $loader = new Autoloader();
        $loader->register();
        $loader->addNamespace('App', __DIR__);

        date_default_timezone_set('Europe/Vilnius');

        $logFileName = 'app_log.txt';

        $handler = new FileHandler($logFileName);
        $logger = new Logger($handler);

        $memcached = new Memcached();
        $memcached->addServer('localhost', 11211);
        $cache = new Cache($memcached, $logger);

        $resultVisualizationService = new ResultVisualizationService($logger, $cache);

        $word = UserInputService::readWordToHyphenated();

        $resultVisualizationService->startAppLogger();

        $timer = new Timer();
        $timer->startTimer();

        $syllableArray = FileService::readDataFromFile( '/Files/hyphen.txt');
        $wordArray = FileService::readDataFromFile( '/Files/words.txt');
        $paragraphArray = FileService::readDataFromFile( '/Files/paragraph.txt');


        $regexHyphenationService = new RegexHyphenationService($syllableArray);
        $hyphenationService = new HyphenationService($syllableArray);

        $singleWord[] = $word;
        $resultVisualizationService->visualizeResults($hyphenationService->hyphenateWord($singleWord),
            "Printing singular word... \n");

        $ParagraphHyphenationService = new ParagraphHyphenationService($paragraphArray, $hyphenationService);
        $finalParagraphArray = $ParagraphHyphenationService->hyphenateParagraph();

        $resultVisualizationService->visualizeResults($finalParagraphArray,
            "Printing hyphenated paragraph (Done with str_* based hyphenation algorithm)... \n");
        FileService::printDataToFile('/Files/nonRegexParagraph.txt', $finalParagraphArray);

        $ParagraphRegexHyphenationService = new ParagraphHyphenationService($paragraphArray, $regexHyphenationService);
        $finalRegexParagraphArray = $ParagraphRegexHyphenationService->hyphenateParagraph();

        $resultVisualizationService->visualizeResults($finalRegexParagraphArray,
            "Printing hyphenated paragraph (Done with regex based hyphenation algorithm)... \n");
        FileService::printDataToFile('/Files/regexParagraph.txt', $finalParagraphArray);

        $timer->endTimer();
        $timeSpent = $timer->getTimeSpent();

        $resultVisualizationService->VisualizeString("Time spent {$timeSpent} milliseconds\n");
        $resultVisualizationService->endAppLogger();
    }
}

$app = new Main();
$app->run();