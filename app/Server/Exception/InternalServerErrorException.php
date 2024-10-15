<?php

declare(strict_types=1);

namespace App\Server\Exception;

use App\Server\Enumerators\ResponseCode;

class InternalServerErrorException extends HttpException
{
    public function __construct(string $message = 'Internal server error', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(ResponseCode::INTERNAL_SERVER_ERROR, $message, $code, $previous);
    }
}
