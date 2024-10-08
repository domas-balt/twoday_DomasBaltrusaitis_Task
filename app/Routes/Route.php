<?php

namespace App\Routes;

use App\Controllers\ControllerInterface;

final readonly class Route
{
    public function __construct(
        private string $route,
        private string $method,
        private ControllerInterface $controller,
        private string $action
    ) {
    }

    public function getActionCallback(): callable
    {
        return [$this->controller, $this->action];
    }

    public function matches(string $endpoint, string $method): bool
    {
        return $this->route === $endpoint && $this->method === $method;
    }
}
