<?php

namespace App\Logger;

use App\Interfaces\LoggerInterface;

abstract class AbstractLogger implements LoggerInterface
{
    use LoggerTrait;
}