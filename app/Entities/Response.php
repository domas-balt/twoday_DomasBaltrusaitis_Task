<?php

declare(strict_types=1);

namespace App\Entities;

readonly class Response
{
    public function __construct(
        private string $statusCodeHeader,
        private ?string $body,
    ) {
    }

    public function getStatusCodeHeader(): string
    {
        return $this->statusCodeHeader;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }
}
