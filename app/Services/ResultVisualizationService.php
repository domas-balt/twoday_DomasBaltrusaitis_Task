<?php

declare(strict_types=1);

namespace App\Services;

use App\Caching\Cache;
use App\Entities\SelectedSyllable;
use App\Entities\Syllable;
use App\Logger\Logger;
use App\Logger\LogLevel;

class ResultVisualizationService
{
    private const string SEPARATOR = '/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/';

    public function __construct(
        private readonly Logger $logger,
    ) {
    }

    public function visualizeResults(array $hyphenatedResults, string $infoString): void
    {
        print(self::SEPARATOR . PHP_EOL . $infoString . PHP_EOL . self::SEPARATOR . PHP_EOL);

        foreach ($hyphenatedResults as $result) {
            $this->logger->log(LogLevel::INFO, "Hyphenated word <{$result}>");
            print_r("> {$result} \n");
        }
    }

    /**
     * @param Syllable[] $syllables
     */
    public function visualizeSyllables(array $syllables, string $infoString): void
    {
        print(self::SEPARATOR . PHP_EOL . $infoString . PHP_EOL . self::SEPARATOR . PHP_EOL);

        foreach ($syllables as $syllable) {
            echo "> {$syllable->getPattern()}" . PHP_EOL;
        }
    }

    /**
     * @param SelectedSyllable[] $selectedSyllables
     */
    public function visualizeSelectedSyllables(array $selectedSyllables): void
    {
        foreach ($selectedSyllables as $selectedSyllable) {
            echo "> {$selectedSyllable->getText()}" . PHP_EOL;
        }
    }

    public function visualizeString(string $stringToPrint): void
    {
        echo $stringToPrint . "\n";
        $this->logger->log(LogLevel::INFO, $stringToPrint);
    }
}
