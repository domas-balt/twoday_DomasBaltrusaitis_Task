<?php

declare(strict_types=1);

namespace App;

require_once 'Autoloader.php';

use App\Controllers\WordController;
use App\Database\DBConnection;
use App\Logger\Handler\LogHandler;
use App\Logger\Logger;
use App\Repositories\WordRepository;
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
        $uri = explode('/', $uri);

        if ($uri[1] !== 'words') {
            header("HTTP/1.1 404 Not Found");
            exit();
        }

        $wordId = null;

        if (isset($uri[2])) {
            $wordId = (int) $uri[2];
        }

        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $wordController = new WordController($dbConnection, $requestMethod, $wordId, $wordRepository);
        $wordController->processRequest();
    }
}

$localServer = new LocalServer();
$localServer->run();
