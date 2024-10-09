<?php

namespace App\Routes;

use App\Controllers\ControllerInterface;

final readonly class Route
{
    public function __construct(
        private string $method,
        private string $route,
        private ControllerInterface $controller,
        private string $action
    ) {
    }

    public function getActionCallback(): callable
    {
        return [$this->controller, $this->action];
    }

    public function matchesWithParameters(string $method, string $uri): bool
    {
        $regexPattern = preg_replace('/\{[a-zA-Z_]+\}/', '(\d+)', $this->route);

        $regexPattern = preg_replace('/\//', '\/', $regexPattern);

        if(preg_match('/^' . $regexPattern . '$/', $uri) && $method === $this->method) {
            return true;
        }

        return false;
    }
}
