<?php

declare(strict_types=1);

namespace App;

require_once 'Autoloader.php';

use App\Database\DBConnection;
use App\Entities\HyphenatedWord;
use App\Entities\Syllable;
use App\Entities\Word;
use App\Enumerators\AppType;
use App\Logger\Handler\LogHandler;
use App\Logger\Logger;
use App\Providers\CLIWordProvider;
use App\Providers\DatabaseSyllableProvider;
use App\Providers\DatabaseWordProvider;
use App\Providers\FileSyllableProvider;
use App\Providers\FileWordProvider;
use App\Providers\SyllableProviderInterface;
use App\Providers\WordProviderInterface;
use App\Repositories\HyphenatedWordRepository;
use App\Repositories\SyllableRepository;
use App\Repositories\WordRepository;
use App\Services\DatabaseHyphenationManagementService;
use App\Services\FileService;
use App\Services\HyphenationService;
use App\Services\ParagraphHyphenationService;
use App\Services\RegexHyphenationService;
use App\Services\ResultVisualizationService;
use App\Services\TransactionService;
use App\Services\UserInputService;
use App\Utilities\Timer;
use JetBrains\PhpStorm\ArrayShape;

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

        FileService::readEnvFile('/var/.env');

        $dbConnection = DBConnection::tryConnect();
        $timer = new Timer();
        $handler = new LogHandler('/var/app_log.txt');
        $logger = new Logger($handler);
        $wordRepository = new WordRepository($dbConnection);
        $hyphenatedWordRepository = new HyphenatedWordRepository($dbConnection);
        $syllableRepository = new SyllableRepository($dbConnection);
        $userInputService = new UserInputService($wordRepository, $syllableRepository);
        $resultVisualizationService = new ResultVisualizationService($logger);

        $applicationType = $userInputService->checkUserArgInput($argv[1]);
//        $userInputService->askAboutDatabaseFileUpdates();
//        $isDbSource = $userInputService->chooseHyphenationSource();
        $isDbSource = true;

        $logger->logStartOfApp();
        $timer->startTimer();

        if ($isDbSource) {
            $transactionService = new TransactionService($hyphenatedWordRepository, $syllableRepository, $dbConnection);
            $words = (new DatabaseWordProvider($wordRepository))->getWords();
            $syllables = (new DatabaseSyllableProvider($syllableRepository))->getSyllables();

            $dbHyphenationManagementService = new DatabaseHyphenationManagementService(
                $transactionService,
                new ParagraphHyphenationService(new HyphenationService($syllables)),
                $wordRepository,
                $syllableRepository,
                $hyphenatedWordRepository,
            );

            $result = $dbHyphenationManagementService->manageHyphenation($words);
        } else {
            $words = $applicationType === AppType::File
                ? (new FileWordProvider('./var/words.txt'))->getWords()
                : (new CLIWordProvider($userInputService))->getWords();

            $syllables = (new FileSyllableProvider())->getSyllables();

            $service = new Service(
                new ParagraphHyphenationService(new HyphenationService($syllables)),
            );

            $result = $service->serviceMethod($words);
        }

        foreach ($result as $data) {
            $resultVisualizationService->visualizeString($data['hyphenated_word']->getText());

            $resultVisualizationService->visualizeSelectedSyllables(
                $data['syllables'],
                "These syllables were used in this word's hyphenation",
            );

            $resultVisualizationService->visualizeString('--------------------');
        }

        $timer->endTimer();
        $timeSpent = $timer->getTimeSpent();
        $resultVisualizationService->VisualizeString("<< Time spent {$timeSpent} seconds >>\n");
        $logger->logEndOfApp();




//        $syllables = $syllableRepository->getAllSyllablesByHyphenatedWordId($hyphenatedWord->getId());
//
//
//        $finalParagraphLines = $paragraphHyphenationService->hyphenateParagraph();
//
//        $resultVisualizationService->visualizeResults($finalParagraphLines,
//            "[INFO] Printing hyphenated paragraph (Done with str_* based hyphenation algorithm)... \n");
//        FileService::printDataToFile('/var/nonRegexParagraph.txt', $finalParagraphLines);
//
//        if ($applicationType === $userInputService::APP_TYPE_DATABASE) {
//            $wordRepository->insertManyHyphenatedWords($finalParagraphLines);
//        }
//
//        $finalRegexParagraphLines = $paragraphRegexHyphenationService->hyphenateParagraph();
//
//        $resultVisualizationService->visualizeResults($finalRegexParagraphLines,
//            "[INFO] Printing hyphenated paragraph (Done with regex based hyphenation algorithm)... \n");
//        FileService::printDataToFile('/var/regexParagraph.txt', $finalRegexParagraphLines);



    }

//    private function getSyllables(bool $isDbSource): array
//    {
//        if ($isDbSource) {
//            return $syllableRepository->getAllSyllables();
//        }
//
//        $syllableFromFile = FileService::readDataFromFile('/var/hyphen.txt');
//
//        $syllables = [];
//
//        foreach ($syllableFromFile as $key => $syllablePattern) {
//            $syllables[] = new Syllable($key, $syllablePattern);
//        }
//
//        return $syllables;
//    }
}

$app = new Main();
//$app->run($argv);
$app->run([
    1 => 'word',
    2 => '/var/paragraph.txt'
]);
