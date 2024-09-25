<?php

namespace App\Logger;

use App\Logger\Handler\HandlerInterface;
use DateTimeInterface;

class Logger extends AbstractILogger
{
    private $handler;

    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }
    public function log(LogLevel $level, $message, array $context = []): void
    {
        $this->handler->handle([
            'message' => self::interpolate((string)$message, $context),
            'level' => strtoupper($level->value),
            'timestamp' => (new \DateTimeImmutable())->format(DateTimeInterface::W3C),
        ]);
    }

    protected static function interpolate(string $message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_string($val) || method_exists($val, '__toString')) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }
}