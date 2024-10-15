<?php

declare(strict_types=1);

namespace App\Server\Entities;

readonly class Word implements \JsonSerializable
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

    public function jsonSerialize(): mixed
    {
        return (object) get_object_vars($this);
    }
}
