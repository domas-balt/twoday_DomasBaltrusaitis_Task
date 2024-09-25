<?php

namespace App\Services;

use App\Caching\Cache;
use App\Logger\Logger;
use App\Logger\LogLevel;

class ResultVisualizationService
{
    private Logger $logger;
    private Cache $cache;

    public function __construct(Logger $logger, Cache $cache)
    {
        $this->logger = $logger;
        $this->cache = $cache;
    }

    public function logSelectedSyllables(array $selectedSyllableArray) : void
    {
        foreach ($selectedSyllableArray as $syllable) {
            $this->logger->log(LogLevel::INFO, "Selected syllable: {syllable}", array("syllable" => $syllable));
        }
    }

    public function visualizeResults(string $finalWord, string $initialWord) : void
    {
        $finalWord = self::processFinalWord($finalWord);
        print_r("Final word:\n" . $finalWord . "\n");

        $this->cache->set($initialWord, $finalWord);
        $this->logger->log(LogLevel::INFO, "Final word: {$finalWord}");
    }

    private function processFinalWord(string $finalWord) : string
    {
        $finalWord = str_replace(".", "", $finalWord);
        $finalWord = str_replace(" ", "", $finalWord);
        $finalWord = ltrim($finalWord, "-");
        return rtrim($finalWord, "-");
    }

    public function startAppLogger() : void
    {
        $this->logger->log(LogLevel::INFO, "<<< STARTING APP >>>");
    }

    public function endAppLogger($startTime, $endTime) : void
    {
        echo "The process took: " . ($endTime - $startTime) / 1000000 . "ms.\n";
        $this->logger->log(LogLevel::INFO, "The process took: " . ($endTime - $startTime) / 1000000 . "ms.");
        $this->logger->log(LogLevel::INFO, "<<< STOPPING APP >>>");
    }

    public function printString(string $stringToPrint) : void
    {
        echo $stringToPrint . "\n";
        $this->logger->log(LogLevel::INFO, $stringToPrint);
    }
}