<?php

declare(strict_types=1);

namespace App\Enumerators;

enum ResponseCode: int
{
    case CREATED = 201;
    case OK = 200;
    case NOT_FOUND = 404;
    case BAD_REQUEST = 400;
    case UNPROCESSABLE_ENTITY = 422;
    case INTERNAL_SERVER_ERROR = 500;
}
