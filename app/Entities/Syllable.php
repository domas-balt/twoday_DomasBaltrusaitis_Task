<?php

declare(strict_types=1);

namespace App\Entities;

class Syllable
{
    public function __construct(
        private int $id,
        private string $pattern
    ) {
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }
}
