<?php

namespace App\Server\Routes;

use App\Server\Controllers\ControllerInterface;
use App\Server\Enumerators\HttpMethods;

final readonly class Route
{
    public function __construct(
        private HttpMethods $method,
        private string $route,
        private ControllerInterface $controller,
        private string $action
    ) {
    }

    public function getActionCallback(): callable
    {
        return [$this->controller, $this->action];
    }

    public function matches(string $method, string $uri): bool
    {
        $regexPattern = preg_replace('/\{[a-zA-Z_]+\}/', '(\d+)', $this->route);
        $regexPattern = preg_replace('/\//', '\/', $regexPattern);

        return preg_match('/^' . $regexPattern . '$/', $uri) && $method === $this->method->value;
    }
}
