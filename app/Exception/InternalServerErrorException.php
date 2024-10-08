<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enumerators\ResponseCode;

class InternalServerErrorException extends HttpException
{
    public function __construct(string $message = null, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(ResponseCode::INTERNAL_SERVER_ERROR, $message, $code, $previous);
    }
}
