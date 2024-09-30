<?php

declare(strict_types=1);

namespace App;

require_once 'Autoloader.php';

use App\Logger\Handler\LogHandler;
use App\Logger\Logger;
use App\Services\FileService;
use App\Services\HyphenationService;
use App\Services\ParagraphHyphenationService;
use App\Services\RegexHyphenationService;
use App\Services\ResultVisualizationService;
use App\Utilities\Timer;

class Main
{
    public function run(array $argv = []): void
    {
        if (count($argv) <= 1 || !is_string($argv[1]) || !file_exists(__DIR__ . $argv[1])) {
            throw new \Exception(\InvalidArgumentException::class);
        }

        $loader = new Autoloader();
        $loader->register();
        $loader->addNamespace('App', __DIR__);

        date_default_timezone_set('Europe/Vilnius');

        $logFileName = '/var/app_log.txt';

        $syllableArray = FileService::readDataFromFile( '/var/hyphen.txt');
        $wordArray = FileService::readDataFromFile($argv[1]);

        $timer = new Timer();
        $handler = new LogHandler($logFileName);
        $logger = new Logger($handler);
        $resultVisualizationService = new ResultVisualizationService($logger);
        $regexHyphenationService = new RegexHyphenationService($syllableArray);
        $hyphenationService = new HyphenationService($syllableArray);
        $paragraphHyphenationService = new ParagraphHyphenationService($wordArray, $hyphenationService);
        $paragraphRegexHyphenationService = new ParagraphHyphenationService($wordArray, $regexHyphenationService);

        $logger->logStartOfApp();

        $timer->startTimer();

        $finalParagraphArray = $paragraphHyphenationService->hyphenateParagraph();

        $resultVisualizationService->visualizeResults($finalParagraphArray,
            "Printing hyphenated paragraph (Done with str_* based hyphenation algorithm)... \n");
        FileService::printDataToFile('/var/nonRegexParagraph.txt', $finalParagraphArray);

        $finalRegexParagraphArray = $paragraphRegexHyphenationService->hyphenateParagraph();

        $resultVisualizationService->visualizeResults($finalRegexParagraphArray,
            "Printing hyphenated paragraph (Done with regex based hyphenation algorithm)... \n");
        FileService::printDataToFile('/var/regexParagraph.txt', $finalRegexParagraphArray);

        $timer->endTimer();
        $timeSpent = $timer->getTimeSpent();

        $resultVisualizationService->VisualizeString("<< Time spent {$timeSpent} seconds >>\n");
        $logger->logEndOfApp();
    }
}

$app = new Main();
//$app->run($argv);
$app->run([
    '',
    '/var/paragraph.txt'
]);
