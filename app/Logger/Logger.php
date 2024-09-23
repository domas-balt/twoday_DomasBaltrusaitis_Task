<?php

namespace App\Logger;

use App\Interfaces\LoggerInterface;

class Logger extends AbstractLogger
{
    private $logger;

    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->logger->error($level, $message);
    }
}