<?php

namespace App\Logger;

use App\Interfaces\ILogger;

abstract class AbstractILogger implements ILogger
{
    use LoggerTrait;
}