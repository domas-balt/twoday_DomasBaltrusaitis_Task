<?php

declare(strict_types=1);

namespace App\Server\Enumerators;

enum SqlStatement: string
{
    case SELECT = 'SELECT';
    case INSERT = 'INSERT';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
}
