<?php

declare(strict_types=1);

namespace App\Server;

require_once __DIR__ . '/../../vendor/autoload.php';
use App\Server\Controllers\WordController;
use App\Server\Database\DatabaseConnection;
use App\Server\Database\QueryBuilder\MySqlQueryBuilder;
use App\Server\Enumerators\HttpMethods;
use App\Server\Exception\HttpException;
use App\Server\Repositories\WordRepository;
use App\Server\Routes\Route;
use App\Server\Routes\RouteManager;
use App\Server\Services\FileService;

class LocalServer
{
    public function run(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');

        $request = $_SERVER['REQUEST_METHOD'];

        if ($request === 'OPTIONS') {
            return;
        }

        FileService::readEnvFile('/var/.env');

        $databaseConnection = DatabaseConnection::tryConnect();
        $queryBuilder = new MySqlQueryBuilder();
        $wordRepository = new WordRepository($queryBuilder, $databaseConnection);

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
