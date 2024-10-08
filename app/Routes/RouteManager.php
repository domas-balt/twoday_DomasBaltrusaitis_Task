<?php

declare(strict_types=1);

namespace App\Routes;

use App\Controllers\WordController;
use App\Enumerators\RouteEntityType;
use App\Exception\InternalServerErrorException;
use App\Exception\NotFoundException;

readonly class RouteManager
{
    /** @var Route[]  */
    private array $routes;

    public function __construct(
//        private array $uri
    ) {
        $wordController = new WordController();

        $this->routes = [
            new Route('GET', '/words', $wordController, 'list'),
            new Route('GET', '/words/{id}', $wordController, 'get'),
        ];
    }

    public function processRequest(string $uri, string $method): Response
    {
        $endpoint = '/words'; // TODO: Extract from uri
        $parameters = [1]; // TODO: Extract from uri

        foreach ($this->routes as $route) {
            if ($route->matches($endpoint, $method)) {
                $callback = $route->getActionCallback();

                return call_user_func($callback, ...$parameters);
            }
        }

        throw new NotFoundException('Not found route');
    }
}
