<?php

declare(strict_types=1);

namespace App\Entities;

class SelectedSyllable
{
    public function __construct(
        private int $id,
        private string $text
    ){
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }
}
