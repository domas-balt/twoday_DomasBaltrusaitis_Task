<?php

declare(strict_types=1);

namespace App\Server\Entities;

readonly class SelectedSyllable
{
    public function __construct(
        private int $id,
        private string $text
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }
}
