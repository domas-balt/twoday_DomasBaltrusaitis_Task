<?php

declare(strict_types=1);

namespace App\Container;

use App\Database\DBConnection;
use App\Database\QueryBuilder\MySqlQueryBuilder;
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
use App\Services\HyphenationService;
use App\Services\ParagraphHyphenationService;
use App\Services\ResultVisualizationService;
use App\Services\TransactionService;
use App\Services\UserInputService;
use App\Utilities\Timer;

class DependencySetter
{
    public static function setAllDependencies(DependencyContainer $dependencyContainer): void
    {
        $dependencyContainer->set('handler', function(){
            return new LogHandler('/var/app_log.txt');
        });

        $dependencyContainer->set('logger', function($dependencyContainer) {
            $handler = $dependencyContainer->get('handler');
            return new Logger($handler);
        });

        $dependencyContainer->set('database', function() {
            return DBConnection::tryConnect();
        });

        $dependencyContainer->set('mySqlQueryBuilder', function() {
            return new MySqlQueryBuilder();
        });

        $dependencyContainer->set('timer', function() {
            return new Timer();
        });

        $dependencyContainer->set('wordRepository', function($dependencyContainer) {
            $mySqlQueryBuilder = $dependencyContainer->get('mySqlQueryBuilder');
            $dbConnection = $dependencyContainer->get('database');
            $logger = $dependencyContainer->get('logger');

            return new WordRepository($mySqlQueryBuilder, $dbConnection, $logger);
        });

        $dependencyContainer->set('selectedSyllableRepository', function($dependencyContainer) {
            $mySqlQueryBuilder = $dependencyContainer->get('mySqlQueryBuilder');
            $dbConnection = $dependencyContainer->get('database');

            return new SelectedSyllableRepository($mySqlQueryBuilder, $dbConnection);
        });

        $dependencyContainer->set('hyphenatedWordRepository', function($dependencyContainer) {
            $mySqlQueryBuilder = $dependencyContainer->get('mySqlQueryBuilder');
            $dbConnection = $dependencyContainer->get('database');

            return new HyphenatedWordRepository($mySqlQueryBuilder, $dbConnection);
        });

        $dependencyContainer->set('syllableRepository', function($dependencyContainer) {
            $mySqlQueryBuilder = $dependencyContainer->get('mySqlQueryBuilder');
            $dbConnection = $dependencyContainer->get('database');
            $logger = $dependencyContainer->get('logger');

            return new SyllableRepository($mySqlQueryBuilder, $dbConnection, $logger);
        });

        $dependencyContainer->set('userInputService', function($dependencyContainer) {
            $wordRepository = $dependencyContainer->get('wordRepository');
            $syllableRepository = $dependencyContainer->get('syllableRepository');

            return new UserInputService($wordRepository, $syllableRepository);
        });

        $dependencyContainer->set('resultVisualizationService', function($dependencyContainer) {
            $logger = $dependencyContainer->get('logger');

            return new ResultVisualizationService($logger);
        });

        $dependencyContainer->set('transactionService', function($dependencyContainer) {
            $hyphenatedWordRepository = $dependencyContainer->get('hyphenatedWordRepository');
            $syllableRepository = $dependencyContainer->get('syllableRepository');
            $selectedSyllableRepository = $dependencyContainer->get('selectedSyllableRepository');
            $dbConnection = $dependencyContainer->get('database');

            return new TransactionService($hyphenatedWordRepository, $syllableRepository, $selectedSyllableRepository, $dbConnection);
        });

        $dependencyContainer->set('databaseWordProvider', function($dependencyContainer) {
            $wordRepository = $dependencyContainer->get('wordRepository');

            return new DatabaseWordProvider($wordRepository);
        });

        $dependencyContainer->set('fileWordProvider', function($dependencyContainer) {
            return new FileWordProvider('/var/paragraph.txt');
        });

        $dependencyContainer->set('databaseSyllableProvider', function($dependencyContainer) {
            $syllableRepository = $dependencyContainer->get('syllableRepository');

            return new DatabaseSyllableProvider($syllableRepository);
        });

        $dependencyContainer->set('fileSyllableProvider', function($dependencyContainer) {
            return new FileSyllableProvider();
        });

        $dependencyContainer->set('cliWordProvider', function($dependencyContainer) {
            $userInputService = $dependencyContainer->get('userInputService');

            return new CliWordProvider($userInputService);
        });

        $dependencyContainer->set('hyphenationServiceDB', function($dependencyContainer) {
            $databaseSyllableProvider = $dependencyContainer->get('databaseSyllableProvider');

            return new HyphenationService($databaseSyllableProvider->getSyllables());
        });

        $dependencyContainer->set('hyphenationServiceFile', function($dependencyContainer) {
            $fileSyllableProvider = $dependencyContainer->get('fileSyllableProvider');

            return new HyphenationService($fileSyllableProvider->getSyllables());
        });

        $dependencyContainer->set('paragraphHyphenationServiceDB', function($dependencyContainer) {
            $hyphenationServiceDB = $dependencyContainer->get('hyphenationServiceDB');

            return new ParagraphHyphenationService($hyphenationServiceDB);
        });

        $dependencyContainer->set('paragraphHyphenationServiceFile', function($dependencyContainer) {
            $hyphenationServiceFile = $dependencyContainer->get('hyphenationServiceFile');

            return new ParagraphHyphenationService($hyphenationServiceFile);
        });

        $dependencyContainer->set('databaseHyphenationManagementService', function($dependencyContainer) {
            $transactionService = $dependencyContainer->get('transactionService');
            $paragraphHyphenationServiceDB = $dependencyContainer->get('paragraphHyphenationServiceDB');
            $wordRepository = $dependencyContainer->get('wordRepository');
            $syllableRepository = $dependencyContainer->get('syllableRepository');
            $hyphenatedWordRepository = $dependencyContainer->get('hyphenatedWordRepository');

            return new DatabaseHyphenationManagementService($transactionService, $paragraphHyphenationServiceDB, $wordRepository, $syllableRepository, $hyphenatedWordRepository);
        });

        $dependencyContainer->set('basicHyphenationManagementServiceFile', function($dependencyContainer) {
            $paragraphHyphenationServiceFile = $dependencyContainer->get('paragraphHyphenationServiceFile');

            return new BasicHyphenationManagementService($paragraphHyphenationServiceFile);
        });

        $dependencyContainer->set('basicHyphenationManagementServiceDB', function($dependencyContainer) {
            $paragraphHyphenationServiceDb = $dependencyContainer->get('paragraphHyphenationServiceDB');

            return new BasicHyphenationManagementService($paragraphHyphenationServiceDb);
        });
    }
}
