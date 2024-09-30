<?php

declare(strict_types=1);

namespace App\Utilities;

class Timer
{
    private float $startTime;
    private float $endTime;

    public function startTimer(): void
    {
        $this->startTime = microtime(true);
    }

    public function endTimer(): void
    {
        $this->endTime = microtime(true);
    }

    public function getTimeSpent(): string
    {
        return number_format(($this->endTime - $this->startTime), 2, '.', '');
    }
}
