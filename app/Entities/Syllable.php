<?php

declare(strict_types=1);

namespace App\Entities;

class Syllable
{
    public function __construct(
        private int $id,
        private string $pattern
    ){
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setPattern(string $pattern): void
    {
        $this->pattern = $pattern;
    }
}
