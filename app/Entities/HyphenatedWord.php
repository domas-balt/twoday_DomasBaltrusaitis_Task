<?php

declare(strict_types=1);

namespace App\Entities;

class HyphenatedWord
{
    public function __construct(
        private int $id,
        private string $text,
        private int $wordId
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
