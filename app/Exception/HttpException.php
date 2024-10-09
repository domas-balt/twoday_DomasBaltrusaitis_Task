<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enumerators\ResponseCode;
use Throwable;

class HttpException extends \Exception
{
    private ResponseCode $responseCode;

    public function __construct(ResponseCode $responseCode, string $message = '', int $code = 0, Throwable $previous = null)
    {
        $this->responseCode = $responseCode;
        parent::__construct($message, $responseCode->value, $previous);
    }

    public function getResponseCode(): int
    {
        return $this->responseCode->value;
    }
}
