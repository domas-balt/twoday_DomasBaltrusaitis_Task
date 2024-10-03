<?php

declare(strict_types=1);

namespace App\Entities;

class Syllable
{
    private int $id;
    private string $pattern;

    public function __construct(int $id, string $pattern)
    {
        $this->id = $id;
        $this->pattern = $pattern;
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
