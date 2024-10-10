<?php

declare(strict_types=1);

namespace App\Entities;

use App\Enumerators\ResponseCode;

readonly class Response
{
    public function __construct(
        private ResponseCode $responseCode,
        private ?string $body,
    ) {
    }

    public function getResponseCode(): int
    {
        return $this->responseCode->value;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }
}
