<?php

declare(strict_types=1);

namespace App\Entities;

readonly class Word
{
    public function __construct(
        private int $id,
        private string $text
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
