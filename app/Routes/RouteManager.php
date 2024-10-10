<?php

declare(strict_types=1);

namespace App\Routes;

use App\Entities\Response;
use App\Exception\NotFoundException;

readonly class RouteManager
{
    /** @param Route[] $routes */
    public function __construct(
        private array $routes,
    ) {
    }

    public function processRequest(string $uri, string $method): Response
    {
        foreach ($this->routes as $route) {
            if (!$route->matches($method, $uri)) {
                continue;
            }

            $parameters = $this->extractParameters($uri);

            $callback = $route->getActionCallback();

            return call_user_func($callback, ...$parameters);
        }

        throw new NotFoundException('Not found route');
    }

    private function extractParameters(string $uri): array
    {
        preg_match('/\d+/', $uri, $matches);

        $parts = parse_url($uri);

        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
            $matches = array_merge($matches, $query);
        }

        return $matches;
    }
}
