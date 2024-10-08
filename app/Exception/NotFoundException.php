<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enumerators\ResponseCode;

class NotFoundException extends HttpException
{
    public function __construct(string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct(ResponseCode::NOT_FOUND, $message, $code, $previous);
    }

}
