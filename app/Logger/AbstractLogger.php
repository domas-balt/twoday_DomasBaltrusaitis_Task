<?php
declare(strict_types=1);

namespace App\Logger;

abstract class AbstractLogger implements LoggerInterface
{
    use LoggerTrait;
}