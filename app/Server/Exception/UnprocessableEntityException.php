<?php

declare(strict_types=1);

namespace App\Server\Exception;

use App\Server\Enumerators\ResponseCode;

class UnprocessableEntityException extends HttpException
{
    public function __construct(string $message = 'Unprocessable entity', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(ResponseCode::UNPROCESSABLE_ENTITY, $message, $code, $previous);
    }
}
