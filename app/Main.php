<?php

declare(strict_types=1);

namespace App;

require_once 'Autoloader.php';

use App\Container\DependencyContainer;
use App\Container\DependencyConfigurator;
use App\Database\DBConnection;
use App\Database\QueryBuilder\MySqlQueryBuilder;
use App\Enumerators\AppType;
use App\Logger\Handler\LogHandler;
use App\Logger\Logger;
use App\Providers\CliWordProvider;
use App\Providers\DatabaseSyllableProvider;
use App\Providers\DatabaseWordProvider;
use App\Providers\FileSyllableProvider;
use App\Providers\FileWordProvider;
use App\Repositories\HyphenatedWordRepository;
use App\Repositories\SelectedSyllableRepository;
use App\Repositories\SyllableRepository;
use App\Repositories\WordRepository;
use App\Services\BasicHyphenationManagementService;
use App\Services\DatabaseHyphenationManagementService;
use App\Services\FileService;
use App\Services\HyphenationService;
use App\Services\ParagraphHyphenationService;
use App\Services\ResultVisualizationService;
use App\Services\TransactionService;
use App\Services\UserInputService;
use App\Utilities\Timer;

class Main
{
    public function run(array $argv = []): void
    {
        if (count($argv) <= 1 || !isset($argv[2]) || !file_exists(__DIR__ . $argv[2])) {
            throw new \Exception(\InvalidArgumentException::class);
        }

        $loader = new Autoloader();
        $loader->register();
        $loader->addNamespace('App', __DIR__);

        FileService::readEnvFile('/var/.env');

        $container = new DependencyContainer();

        DependencyConfigurator::setAllDependencies($container);

        $timer = $container->get('timer');
        $logger = $container->get('logger');

        $userInputService = $container->get('userInputService');
        $resultVisualizationService = $container->get('resultVisualizationService');

        $applicationType = $userInputService->checkUserArgInput($argv[1]);
        $userInputService->askAboutDatabaseFileUpdates();
        $isDbSource = $userInputService->chooseHyphenationSource();

        $logger->logStartOfApp();
        $timer->startTimer();

        if ($applicationType === AppType::DATABASE) {
            $words = ($container->get('databaseWordProvider'))->getWords();

            $dbHyphenationManagementService = $container->get('databaseHyphenationManagementService');

            $result = $dbHyphenationManagementService->manageHyphenation($words);
        } else {
            $words = $applicationType === AppType::FILE
                ? ($container->get('fileWordProvider'))->getWords()
                : ($container->get('cliWordProvider'))->getWords();

            $basicHyphenationManagementService = $isDbSource
                ? $container->get('basicHyphenationManagementServiceDB')
                : $container->get('basicHyphenationManagementServiceFile');

            $result = $basicHyphenationManagementService->manageHyphenation($words);
        }

        foreach ($result as $data) {
            $resultVisualizationService->visualizeString($data['hyphenated_word']->getText());

            if ($applicationType === AppType::WORD) {
                $resultVisualizationService->visualizeSelectedSyllables($data['syllables']);
            }
        }

        $timer->endTimer();
        $timeSpent = $timer->getTimeSpent();
        $resultVisualizationService->VisualizeString("<< Time spent {$timeSpent} seconds >>\n");
        $logger->logEndOfApp();
    }
}

$app = new Main();
$app->run($argv);
