<?php

declare(strict_types=1);

namespace App\Server\Enumerators;

enum AppType
{
    case FILE;
    case WORD;
    case DATABASE;
}

