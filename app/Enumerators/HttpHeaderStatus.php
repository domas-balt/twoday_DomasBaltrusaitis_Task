<?php

declare(strict_types=1);

namespace App\Enumerators;

enum HttpHeaderStatus: string
{
    case OK = 'HTTP/1.1 200 OK';
    case CREATED = 'HTTP/1.1 201 Created';
}
