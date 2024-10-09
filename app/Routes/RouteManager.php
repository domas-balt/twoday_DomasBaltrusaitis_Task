<?php

declare(strict_types=1);

namespace App\Routes;

use App\Controllers\WordController;
use App\Entities\Response;
use App\Exception\NotFoundException;
use App\Repositories\WordRepository;

readonly class RouteManager
{
    /** @var Route[] $routes */
    private array $routes;

    public function __construct(
        private WordRepository $wordRepository,
    ) {
        $wordController = new WordController($wordRepository);

        $this->routes = [
            new Route('GET', '/words', $wordController, 'getAll'),
            new Route('GET', '/words/{id}', $wordController, 'getById'),
            new Route('POST', '/words', $wordController, 'create'),
            new Route('PUT', '/words/{id}', $wordController, 'update'),
            new Route('DELETE', '/words/{id}', $wordController, 'delete'),
        ];
    }

    public function processRequest(string $uri, string $method): Response
    {
        foreach ($this->routes as $route) {
            if ($route->matchesWithParameters($method, $uri)) {
                $parameters = $this->extractParameters($uri);

                $callback = $route->getActionCallback();

                return call_user_func($callback, ...$parameters);
            }
        }

        throw new NotFoundException('Not found route');
    }

    private function extractParameters(string $uri): array
    {
        preg_match('/\d+/', $uri, $matches);

        return $matches;
    }
}
