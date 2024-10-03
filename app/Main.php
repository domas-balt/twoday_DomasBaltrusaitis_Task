<?php

declare(strict_types=1);

namespace App;

require_once 'Autoloader.php';

use App\Database\DBConnection;
use App\Entities\HyphenatedWord;
use App\Entities\Syllable;
use App\Entities\Word;
use App\Logger\Handler\LogHandler;
use App\Logger\Logger;
use App\Repositories\SyllableRepository;
use App\Repositories\WordRepository;
use App\Services\FileService;
use App\Services\HyphenationService;
use App\Services\ParagraphHyphenationService;
use App\Services\RegexHyphenationService;
use App\Services\ResultVisualizationService;
use App\Services\TransactionService;
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
        $syllables = [];
        $wordRepository = new WordRepository($dbConnection);
        $syllableRepository = new SyllableRepository($dbConnection);
        $userInputService = new UserInputService($wordRepository, $syllableRepository);

        $isFile = $userInputService->checkUserArgInput($argv[1]);
//        $userInputService->askAboutDatabaseFileUpdates();
//        $isDbSource = $userInputService->chooseHyphenationSource();
        $isDbSource = true;

        if ($isDbSource) {
            $syllables = $syllableRepository->getAllSyllables();
        } else {
            $syllablePatterns = FileService::readDataFromFile('/var/hyphen.txt');
            foreach ($syllablePatterns as $key => $syllablePattern) {
                $syllables[] = new Syllable($key, $syllablePattern);
            }
        }

        if ($isFile) {
            $words = FileService::readDataFromFile($argv[2]);
        } else {
//            $words[] = $userInputService->readWordToHyphenate();
            $words[0] = 'generator';
        }

        $wordEntity = new Word(0, "");
        $hyphenatedWord = new HyphenatedWord(0, "", 0);
        $hyphenatedWordExists = false;
        $wordExistsDb = false;

        $timer = new Timer();
        $handler = new LogHandler($logFileName);
        $logger = new Logger($handler);
        $transactionService = new TransactionService($wordRepository, $syllableRepository, $dbConnection);
        $resultVisualizationService = new ResultVisualizationService($logger);
        $regexHyphenationService = new RegexHyphenationService($syllables);
        $hyphenationService = new HyphenationService($syllables);
        $paragraphHyphenationService = new ParagraphHyphenationService($words, $hyphenationService);
        $paragraphRegexHyphenationService = new ParagraphHyphenationService($words, $regexHyphenationService);

        $logger->logStartOfApp();

        $timer->startTimer();

        if (!$isFile) {
            $wordExistsDb = $wordRepository->checkIfWordExistsDb($words[0]);
            if ($wordExistsDb) {
                $wordEntity = $wordRepository->findWordByText($words[0]);

                $hyphenatedWordExists = $wordRepository->checkIfHyphenatedWordExistsDb($wordEntity->getId());
                if($hyphenatedWordExists) {
                    $hyphenatedWord = $wordRepository->findHyphenatedWordById($wordEntity->getId());
                }
            }
        }

        if ($wordExistsDb && $hyphenatedWordExists) {
            $finalParagraphLines[0] = $hyphenatedWord->getText();
            $resultVisualizationService->visualizeResults($finalParagraphLines,
                "This word has already been hyphenated. It was found in the Database. No calculations proceeding...");

            $syllables = $syllableRepository->getAllSyllablesByHyphenatedWordId($hyphenatedWord->getId());
            $resultVisualizationService->visualizeSelectedSyllables($syllables,
                "These syllables were used in this word's hyphenation");
        } else {
            $finalParagraphLines = $paragraphHyphenationService->hyphenateParagraph();

            $resultVisualizationService->visualizeResults($finalParagraphLines,
                "[INFO] Printing hyphenated paragraph (Done with str_* based hyphenation algorithm)... \n");
            FileService::printDataToFile('/var/nonRegexParagraph.txt', $finalParagraphLines);

            $finalRegexParagraphLines = $paragraphRegexHyphenationService->hyphenateParagraph();

            $resultVisualizationService->visualizeResults($finalRegexParagraphLines,
                "[INFO] Printing hyphenated paragraph (Done with regex based hyphenation algorithm)... \n");
            FileService::printDataToFile('/var/regexParagraph.txt', $finalRegexParagraphLines);
        }

        if (!$wordExistsDb && $isDbSource)
        {
            $wordEntity = $wordRepository->insertWord($words[0]);
        }

        $timer->endTimer();
        $timeSpent = $timer->getTimeSpent();

        $resultVisualizationService->VisualizeString("<< Time spent {$timeSpent} seconds >>\n");
        $logger->logEndOfApp();

        if (!$isFile && $isDbSource && !$hyphenatedWordExists) {
            $syllablesWithNumbers = $hyphenationService->getSyllables();

            $resultVisualizationService->visualizeSyllables($syllablesWithNumbers,
                "These patterns where used in hyphenating the word:");

            $syllableIds = $transactionService->syllableWordInsertTransaction($finalParagraphLines[0], $wordEntity->getId(), $syllablesWithNumbers);

            $hyphenatedWord = $wordRepository->findHyphenatedWordById($wordEntity->getId());
            $wordRepository->insertHyphenatedWordAndSyllableIds($syllableIds, $hyphenatedWord->getId());
        }
    }
}

$app = new Main();
//$app->run($argv);
$app->run([
    1 => 'word',
    2 => '/var/paragraph.txt'
]);
