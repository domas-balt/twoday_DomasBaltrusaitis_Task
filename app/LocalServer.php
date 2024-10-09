<?php

declare(strict_types=1);

namespace App;

require_once 'Autoloader.php';

use App\Database\DBConnection;
use App\Exception\HttpException;
use App\Logger\Handler\LogHandler;
use App\Logger\Logger;
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

        $routeManager = new RouteManager($wordRepository);

        $response = $routeManager->processRequest($uri, $_SERVER['REQUEST_METHOD']);

        try {
            header($response->getStatusCodeHeader());
            echo $response->getBody();
        } catch (HttpException $exception) {
            header($exception->getResponseHeader());
            echo json_encode($exception->getMessage());
        }
    }
}

$localServer = new LocalServer();
$localServer->run();
