<?php

declare(strict_types=1);

namespace App\Enumerators;

enum HttpMethods: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
}
