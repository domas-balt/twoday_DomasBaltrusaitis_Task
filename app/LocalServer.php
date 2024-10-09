<?php

declare(strict_types=1);

namespace App;

require_once 'Autoloader.php';

use App\Controllers\WordController;
use App\Database\DBConnection;
use App\Enumerators\HttpMethods;
use App\Exception\HttpException;
use App\Logger\Handler\LogHandler;
use App\Logger\Logger;
use App\Repositories\WordRepository;
use App\Routes\Route;
use App\Routes\RouteManager;
use App\Services\FileService;

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

        $wordController = new WordController($wordRepository);

        $wordRoutes = [
            new Route(HttpMethods::GET, '/words', $wordController, 'getAll'),
            new Route(HttpMethods::GET, '/words/{id}', $wordController, 'getById'),
            new Route(HttpMethods::POST, '/words', $wordController, 'create'),
            new Route(HttpMethods::PUT, '/words/{id}', $wordController, 'update'),
            new Route(HttpMethods::DELETE, '/words/{id}', $wordController, 'delete'),
        ];

        $routeManager = new RouteManager($wordRoutes);

        try {
            header("Content-Type: application/json; charset=UTF-8");

            $response = $routeManager->processRequest($uri, $_SERVER['REQUEST_METHOD']);
            http_response_code($response->getResponseCode());

            echo $response->getBody();
        } catch (HttpException $exception) {
            http_response_code($exception->getResponseCode());

            echo json_encode($exception->getMessage());
        }
    }
}

$localServer = new LocalServer();
$localServer->run();
