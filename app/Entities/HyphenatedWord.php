<?php

declare(strict_types=1);

namespace App\Entities;

class HyphenatedWord
{
    private int $id;
    private string $text;
    private int $wordId;

    public function __construct(int $id, string $text, int $wordId)
    {
        $this->id = $id;
        $this->text = $text;
        $this->wordId = $wordId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getWordId(): int
    {
        return $this->wordId;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setWordId(int $wordId): void
    {
        $this->wordId = $wordId;
    }
}
