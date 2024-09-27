<?php
declare(strict_types=1);

namespace App\Logger\Handler;
interface HandlerInterface
{
    public const string DEFAULT_FORMAT = '%timestamp% [%level%]: %message%';
    public function handle(array $variables): void;
}