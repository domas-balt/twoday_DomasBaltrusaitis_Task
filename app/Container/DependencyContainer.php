<?php

declare(strict_types=1);

namespace App\Container;

class DependencyContainer
{
    private array $bindings = [];

    public function set(string $id, callable $factory): void
    {
        $this->bindings[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (!isset($this->bindings[$id])) {
            throw new \Exception("Target binding [$id] does not exist.");
        }

        $factory = $this->bindings[$id];

        return $factory($this);
    }
}
