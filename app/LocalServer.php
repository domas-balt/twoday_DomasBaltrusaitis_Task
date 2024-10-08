<?php

declare(strict_types=1);

namespace App;

require_once 'Autoloader.php';

use App\Controllers\WordController;
use App\Database\DBConnection;
use App\Enumerators\RouteEntityType;
use App\Exception\HttpException;
use App\Exception\InternalServerErrorException;
use App\Logger\Handler\LogHandler;
use App\Logger\Logger;
use App\Logger\LogLevel;
use App\Repositories\WordRepository;
use App\Routes\RouteManager;
use App\Services\FileService;

header("Content-Type: application/json; charset=UTF-8");

class LocalServer
{
    public function run(): void
    {
        $loader = new Autoloader();
        $loader->register();
        $loader->addNamespace('App', __DIR__);

        FileService::readEnvFile('/var/.env');

        $dbConnection = DBConnection::tryConnect();
        $handler = new LogHandler('/var/app_log.txt');
        $logger = new Logger($handler);
        $wordRepository = new WordRepository($dbConnection, $logger);

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $parts = explode('/', $uri);

        foreach ($parts as $part) {
            $part = ltrim($part, "%7B");
            $part = rtrim($part, "%7D");
            $logger->log(LogLevel::INFO, $part);
        }
        $uri  = implode('/', $parts);

        //TODO: ISSIEXTRACTINK IS URL PARAMETRUS IR ENDPOINTA PIRMA ig. KAIP NUSPREST AR PARAMETRAS? GALI EIT PVZ PARAMETRAI TARP {} skliaustu ARBA po klaustuko
        // Pacheckinsi ar prasideda su bracketais, jei jo - tai yra parametras, jei ne - endpointas.
        $routeManager = new RouteManager();

        $callback = $routeManager->processRequest($uri, 'GET');

//        $entityType = $routeManager->checkRouteValidity();
//        $entityId = $routeManager->getRouteEntityId();

        $requestMethod = $_SERVER['REQUEST_METHOD'];

//        switch ($entityType->value) {
//            case RouteEntityType::WORDS->value:
//                $entityController = new WordController($dbConnection, $requestMethod, $entityId, $wordRepository);
//                break;
//
//        }

//        try {
//            $response = $callback();
//
//            echo $response['body'];
//        } catch (HttpException $exception) {
//            header($exception->getResponseHeader());
//            echo json_encode($exception->getMessage());
//        }
    }
}

$localServer = new LocalServer();
$localServer->run();
