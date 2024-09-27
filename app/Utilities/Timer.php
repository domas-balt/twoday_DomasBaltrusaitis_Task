<?php
declare(strict_types=1);

namespace App\Utilities;

class Timer
{
    private float $startTime;
    private float $endTime;

    public function startTimer(): void
    {
        $this->startTime = hrtime(true);
    }

    public function endTimer(): void
    {
        $this->endTime = hrtime(true);
    }

    public function getTimeSpent(): float
    {
        return ($this->endTime - $this->startTime) / 1000000000;
    }
}