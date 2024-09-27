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

    public function visualizeResults(array $hyphenatedResults, string $infoString) : void
    {
        print($infoString);
        foreach ($hyphenatedResults as $result) {
            $this->logger->log(LogLevel::INFO, "Hyphenated word <{$result}>");
            print_r("{$result} \n");
        }
//        $this->cache->set($initialWord, $finalWord); //TODO: Iskelt kitur
    }

    public function visualizeString(string $stringToPrint) : void
    {
        echo $stringToPrint . "\n";
        $this->logger->log(LogLevel::INFO, $stringToPrint);
    }

    public function getProcessedWord(string $finalWord) : string
    {
        return self::processFinalWord($finalWord);
    }

    public function startAppLogger() : void
    {
        $this->logger->log(LogLevel::INFO, "<<< STARTING APP >>>");
    }

    public function endAppLogger() : void
    {
        $this->logger->log(LogLevel::INFO, "<<< STOPPING APP >>>");
    }
}