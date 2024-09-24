<?php

namespace App\Services;

use App\Logger\Logger;
use App\Logger\LogLevel;

class ResultVisualizationService
{
    private Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function logSelectedSyllables(array $selectedSyllableArray) : void
    {
        foreach ($selectedSyllableArray as $syllable) {
            $this->logger->log(LogLevel::INFO, "Selected syllable: {syllable}", array("syllable" => $syllable));
        }
    }

    public function visualizeResults(string $finalWord) : void
    {
        $finalWord = self::processFinalWord($finalWord);
        print_r("Final word:\n" . $finalWord . "\n");

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
}