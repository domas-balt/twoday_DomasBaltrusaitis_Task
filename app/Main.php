<?php

declare(strict_types=1);

namespace App;

require_once 'Autoloader.php';

use App\Database\DBConnection;
use App\Logger\Handler\LogHandler;
use App\Logger\Logger;
use App\Repositories\SyllableRepository;
use App\Repositories\WordRepository;
use App\Services\FileService;
use App\Services\HyphenationService;
use App\Services\ParagraphHyphenationService;
use App\Services\RegexHyphenationService;
use App\Services\ResultVisualizationService;
use App\Services\UserInputService;
use App\Utilities\Timer;

class Main
{
    public function run(array $argv = []): void
    {
        if (count($argv) <= 1 || !is_string($argv[2]) || !file_exists(__DIR__ . $argv[2])) {
            throw new \Exception(\InvalidArgumentException::class);
        }

        $loader = new Autoloader();
        $loader->register();
        $loader->addNamespace('App', __DIR__);

        date_default_timezone_set('Europe/Vilnius');

        $logFileName = '/var/app_log.txt';

        FileService::readEnvFile('/var/.env');

        $dbConnection = DBConnection::tryConnect();
        $wordRepository = new WordRepository($dbConnection);
        $syllableRepository = new SyllableRepository($dbConnection);
        $userInputService = new UserInputService($wordRepository, $syllableRepository);

        $isFile = $userInputService->checkUserArgInput($argv[1]);
        $userInputService->askForDatabaseFileUpdates();
        $isDbSource = $userInputService->chooseHyphenationSource();

        if ($isDbSource) {
            $syllables = $syllableRepository->getAllSyllables();
        } else {
            $syllables = FileService::readDataFromFile('/var/hyphen.txt');
        }

        if ($isFile) {
            $words = FileService::readDataFromFile($argv[1]);
        } else {
            $words[] = $userInputService->readWordToHyphenate();
        }

        $timer = new Timer();
        $handler = new LogHandler($logFileName);
        $logger = new Logger($handler);
        $resultVisualizationService = new ResultVisualizationService($logger);
        $regexHyphenationService = new RegexHyphenationService($syllables);
        $hyphenationService = new HyphenationService($syllables);
        $paragraphHyphenationService = new ParagraphHyphenationService($words, $hyphenationService);
        $paragraphRegexHyphenationService = new ParagraphHyphenationService($words, $regexHyphenationService);

        $logger->logStartOfApp();

        $timer->startTimer();

        $finalParagraphArray = $paragraphHyphenationService->hyphenateParagraph();

        $resultVisualizationService->visualizeResults($finalParagraphArray,
            "[INFO] Printing hyphenated paragraph (Done with str_* based hyphenation algorithm)... \n");
        FileService::printDataToFile('/var/nonRegexParagraph.txt', $finalParagraphArray);

        $finalRegexParagraphArray = $paragraphRegexHyphenationService->hyphenateParagraph();

        $resultVisualizationService->visualizeResults($finalRegexParagraphArray,
            "[INFO] Printing hyphenated paragraph (Done with regex based hyphenation algorithm)... \n");
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
    'word',
    '/var/paragraph.txt'
]);
