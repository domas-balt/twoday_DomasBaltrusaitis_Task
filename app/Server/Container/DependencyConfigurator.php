<?php

declare(strict_types=1);

namespace App\Server\Container;

use App\Server\Database\DatabaseConnection;
use App\Server\Database\QueryBuilder\MySqlQueryBuilder;
use App\Server\Logger\Handler\LogHandler;
use App\Server\Logger\Logger;
use App\Server\Providers\CliWordProvider;
use App\Server\Providers\DatabaseSyllableProvider;
use App\Server\Providers\DatabaseWordProvider;
use App\Server\Providers\FileSyllableProvider;
use App\Server\Providers\FileWordProvider;
use App\Server\Repositories\HyphenatedWordRepository;
use App\Server\Repositories\SelectedSyllableRepository;
use App\Server\Repositories\SyllableRepository;
use App\Server\Repositories\WordRepository;
use App\Server\Services\BasicHyphenationManagementService;
use App\Server\Services\DatabaseHyphenationManagementService;
use App\Server\Services\HyphenationService;
use App\Server\Services\ParagraphHyphenationService;
use App\Server\Services\ResultVisualizationService;
use App\Server\Services\TransactionService;
use App\Server\Services\UserInputService;
use App\Server\Utilities\Timer;

class DependencyConfigurator
{
    public static function setAllDependencies(DependencyContainer $dependencyContainer): void
    {
        $dependencyContainer->set('handler', function(): LogHandler {
            return new LogHandler('/var/app_log.txt');
        });

        $dependencyContainer->set('logger', function(DependencyContainer $dependencyContainer): Logger {
            $handler = $dependencyContainer->get('handler');

            return new Logger($handler);
        });

        $dependencyContainer->set('database', function(): \PDO {
            return DatabaseConnection::tryConnect();
        });

        $dependencyContainer->set('mySqlQueryBuilder', function(): MySqlQueryBuilder {
            return new MySqlQueryBuilder();
        });

        $dependencyContainer->set('timer', function() {
            return new Timer();
        });

        $dependencyContainer->set('wordRepository', function(DependencyContainer $dependencyContainer): WordRepository {
            $mySqlQueryBuilder = $dependencyContainer->get('mySqlQueryBuilder');
            $databaseConnection = $dependencyContainer->get('database');

            return new WordRepository($mySqlQueryBuilder, $databaseConnection);
        });

        $dependencyContainer->set('selectedSyllableRepository', function(DependencyContainer $dependencyContainer): SelectedSyllableRepository {
            $mySqlQueryBuilder = $dependencyContainer->get('mySqlQueryBuilder');
            $databaseConnection = $dependencyContainer->get('database');

            return new SelectedSyllableRepository($mySqlQueryBuilder, $databaseConnection);
        });


        $dependencyContainer->set('hyphenatedWordRepository', function(DependencyContainer $dependencyContainer): HyphenatedWordRepository {
            $mySqlQueryBuilder = $dependencyContainer->get('mySqlQueryBuilder');
            $databaseConnection = $dependencyContainer->get('database');

            return new HyphenatedWordRepository($mySqlQueryBuilder, $databaseConnection);
        });

        $dependencyContainer->set('syllableRepository', function(DependencyContainer $dependencyContainer): SyllableRepository {
            $mySqlQueryBuilder = $dependencyContainer->get('mySqlQueryBuilder');
            $databaseConnection = $dependencyContainer->get('database');
            $logger = $dependencyContainer->get('logger');

            return new SyllableRepository($mySqlQueryBuilder, $databaseConnection, $logger);
        });

        $dependencyContainer->set('userInputService', function(DependencyContainer $dependencyContainer): UserInputService {
            $wordRepository = $dependencyContainer->get('wordRepository');
            $syllableRepository = $dependencyContainer->get('syllableRepository');

            return new UserInputService($wordRepository, $syllableRepository);
        });

        $dependencyContainer->set('resultVisualizationService', function(DependencyContainer $dependencyContainer): ResultVisualizationService {
            $logger = $dependencyContainer->get('logger');

            return new ResultVisualizationService($logger);
        });

        $dependencyContainer->set('transactionService', function(DependencyContainer $dependencyContainer): TransactionService {
            $hyphenatedWordRepository = $dependencyContainer->get('hyphenatedWordRepository');
            $syllableRepository = $dependencyContainer->get('syllableRepository');
            $selectedSyllableRepository = $dependencyContainer->get('selectedSyllableRepository');
            $databaseConnection = $dependencyContainer->get('database');

            return new TransactionService(
                $hyphenatedWordRepository,
                $syllableRepository,
                $selectedSyllableRepository,
                $databaseConnection,
            );
        });

        $dependencyContainer->set('databaseWordProvider', function(DependencyContainer $dependencyContainer): DatabaseWordProvider {
            $wordRepository = $dependencyContainer->get('wordRepository');

            return new DatabaseWordProvider($wordRepository);
        });

        $dependencyContainer->set('fileWordProvider', function(): FileWordProvider {
            return new FileWordProvider('/var/paragraph.txt');
        });

        $dependencyContainer->set('databaseSyllableProvider', function(DependencyContainer $dependencyContainer): DatabaseSyllableProvider {
            $syllableRepository = $dependencyContainer->get('syllableRepository');

            return new DatabaseSyllableProvider($syllableRepository);
        });

        $dependencyContainer->set('fileSyllableProvider', function(): FileSyllableProvider {
            return new FileSyllableProvider();
        });

        $dependencyContainer->set('cliWordProvider', function(DependencyContainer $dependencyContainer): CliWordProvider {
            $userInputService = $dependencyContainer->get('userInputService');

            return new CliWordProvider($userInputService);
        });

        $dependencyContainer->set('databaseHyphenationService', function(DependencyContainer $dependencyContainer): HyphenationService {
            $databaseSyllableProvider = $dependencyContainer->get('databaseSyllableProvider');

            return new HyphenationService($databaseSyllableProvider->getSyllables());
        });

        $dependencyContainer->set('fileHyphenationService', function(DependencyContainer $dependencyContainer) {
            $fileSyllableProvider = $dependencyContainer->get('fileSyllableProvider');

            return new HyphenationService($fileSyllableProvider->getSyllables());
        });

        $dependencyContainer->set('databaseParagraphHyphenationService', function(DependencyContainer $dependencyContainer) {
            $databaseHyphenationService = $dependencyContainer->get('databaseHyphenationService');

            return new ParagraphHyphenationService($databaseHyphenationService);
        });

        $dependencyContainer->set('fileParagraphHyphenationService', function(DependencyContainer $dependencyContainer) {
            $fileHyphenationService = $dependencyContainer->get('fileHyphenationService');

            return new ParagraphHyphenationService($fileHyphenationService);
        });

        $dependencyContainer->set('databaseHyphenationManagementService', function(DependencyContainer $dependencyContainer) {
            $transactionService = $dependencyContainer->get('transactionService');
            $databaseParagraphHyphenationService = $dependencyContainer->get('databaseParagraphHyphenationService');
            $wordRepository = $dependencyContainer->get('wordRepository');
            $syllableRepository = $dependencyContainer->get('syllableRepository');
            $hyphenatedWordRepository = $dependencyContainer->get('hyphenatedWordRepository');

            return new DatabaseHyphenationManagementService(
                $transactionService,
                $databaseParagraphHyphenationService,
                $wordRepository,
                $syllableRepository,
                $hyphenatedWordRepository,
            );
        });

        $dependencyContainer->set('fileBasicHyphenationManagementService', function(DependencyContainer $dependencyContainer) {
            $fileParagraphHyphenationService = $dependencyContainer->get('fileParagraphHyphenationService');

            return new BasicHyphenationManagementService($fileParagraphHyphenationService);
        });

        $dependencyContainer->set('databaseBasicHyphenationManagementService', function(DependencyContainer $dependencyContainer) {
            $databaseParagraphHyphenationService = $dependencyContainer->get('databaseParagraphHyphenationService');

            return new BasicHyphenationManagementService($databaseParagraphHyphenationService);
        });
    }
}
