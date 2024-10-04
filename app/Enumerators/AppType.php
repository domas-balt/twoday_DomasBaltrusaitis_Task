<?php

declare(strict_types=1);

namespace App\Enumerators;

enum AppType
{
    case File;
    case Word;
    case Database;
}
