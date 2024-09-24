<?php

namespace App\Logger\Handler;
interface HandlerInterface
{
    public const DEFAULT_FORMAT = '%timestamp% [%level%]: %message%';
    function handle(array $variables): void;
}