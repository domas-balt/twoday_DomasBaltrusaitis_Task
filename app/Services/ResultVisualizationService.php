<?php

declare(strict_types=1);

namespace App\Services;

use App\Caching\Cache;
use App\Logger\Logger;
use App\Logger\LogLevel;

class ResultVisualizationService
{
    public function __construct(
        private readonly Logger $logger,
    ){
    }

    public function visualizeResults(array $hyphenatedResults, string $infoString): void
    {
        print($infoString);
        foreach ($hyphenatedResults as $result) {
            $this->logger->log(LogLevel::INFO, "Hyphenated word <{$result}>");
            print_r("{$result} \n");
        }
    }

    public function visualizeString(string $stringToPrint): void
    {
        echo $stringToPrint . "\n";
        $this->logger->log(LogLevel::INFO, $stringToPrint);
    }
}
