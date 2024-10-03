<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Syllable;

class HyphenationService implements HyphenationServiceInterface
{
    private array $doubledIndexWords = [];
    private array $selectedSyllables = [];
    private array $finalWords = [];
    private array $patternsWithNumbers = [];
    private array $syllables;

    /**
     * @param Syllable[] $syllables
     */
    public function __construct(array $syllables)
    {
        foreach ($syllables as $syllable) {
            $this->syllables[] = $syllable->getPattern();
        }
    }

    public function getSyllables(): array
    {
        return $this->patternsWithNumbers;
    }

    public function hyphenateWords(array $words): array
    {
        foreach ($words as $key => $word) {
            $this->findUsableSyllables($word);
            $word = $this->findSyllablePositionsInWord($word);
            $this->mergeSyllablesAndWordPositionArrays($word);
            $finalProcessedWord = $this->addHyphensAndWhitespaces();

            $words[$key] = $this->removeTrailingSymbols($finalProcessedWord);

            $this->clearArrays();
        }

        return $words;
    }

    private function findUsableSyllables(string $word): void
    {
        $arrayWithoutNumbers = $this->FilterOutNumbersFromArray($this->syllables);

        foreach ($arrayWithoutNumbers as $key => $syllable) {
            if (
                $this->isFirstSyllable($syllable)
                && str_starts_with($word, substr($syllable, 1))
            ) {
                $this->selectedSyllables[$key] = $syllable;
            }

            if (
                !$this->isFirstSyllable($syllable)
                && !$this->isLastSyllable($syllable)
                && str_contains($word, $syllable)
            ) {
                $this->selectedSyllables[$key] = $syllable;
            }

            if ($this->isLastSyllable($syllable)
                && str_ends_with($word, substr($syllable, 0, -1))
            ) {
                $this->selectedSyllables[$key] = $syllable;
            }
        }

        foreach ($this->selectedSyllables as $key => $value) {
            $this->patternsWithNumbers[$key] = new Syllable($key, $this->syllables[$key]);
        }
    }

    private function findSyllablePositionsInWord(string $word): string
    {
        $word = ".{$word}.";
        $wordChars = str_split($word);

        foreach ($this->selectedSyllables as $key => $pattern) {
            $patternWithoutNumbers = str_split($this->removeNumbersFromString($pattern));

            $fullPatternChars = str_split(str_replace("\n","",$this->patternsWithNumbers[$key]->getPattern()));

            $successfulMatchCount = 0;
            $comparisonBuffer = 0;
            $currentIndex = 0;

            $patternPositions = [];

            while ($successfulMatchCount < count($patternWithoutNumbers)) {
                if ($wordChars[$comparisonBuffer + $currentIndex]
                    !== $patternWithoutNumbers[$currentIndex]
                ) {
                    $successfulMatchCount = 0;
                    $patternPositions = [];
                }

                if ($wordChars[$comparisonBuffer + $currentIndex]
                    === $patternWithoutNumbers[$currentIndex]
                ) {
                    $successfulMatchCount++;
                    $patternPositions[$comparisonBuffer + $currentIndex] = $patternWithoutNumbers[$currentIndex];
                }

                $currentIndex++;

                if ($currentIndex >= count($patternWithoutNumbers)) {
                    $comparisonBuffer++;
                    $currentIndex = 0;
                }

                if ($successfulMatchCount === count($patternWithoutNumbers)) {
                    $this->buildWordWithNumbers($patternPositions, $fullPatternChars);

                    break;
                }
            }
        }

        return $word;
    }

    private function mergeSyllablesAndWordPositionArrays(string $word): void
    {
        ksort($this->finalWords);
        ksort($this->doubledIndexWords);
        
        $wordToHyphenateSplit = str_split($word);
        $wordToHyphenateExpandedIndices = [];

        foreach ($wordToHyphenateSplit as $key => $wordToHyphenateChar) {
            $wordToHyphenateExpandedIndices[$key * 2] = $wordToHyphenateChar;
        }

        $this->finalWords = $this->finalWords + $wordToHyphenateExpandedIndices;
        ksort($this->finalWords);
    }

    private function buildWordWithNumbers(Array $patternWithCharPositions, Array $fullPattern): void
    {
        $patternDoubledIndices = [];

        foreach ($patternWithCharPositions as $key => $patternCharNoNumber) {
            $patternDoubledIndices[$key * 2] = $patternCharNoNumber;
            $this->doubledIndexWords[$key * 2] = $patternCharNoNumber;
        }

        $iterationDoubleKey = array_key_first($patternDoubledIndices);

        for ($i = 0; $i < count($fullPattern); $i++) {
            if (is_numeric($fullPattern[$i])) {
                $patternDoubledIndices[$iterationDoubleKey - 1] = $fullPattern[$i];

                continue;
            }

            $iterationDoubleKey = $iterationDoubleKey + 2;
        }

        ksort($patternDoubledIndices);

        foreach ($patternDoubledIndices as $key => $value) {
            if (!isset($this->finalWords[$key])){
                $this->finalWords[$key] = $value;
            }

            if (isset($this->finalWords[$key]) &&
                is_numeric($value) &&
                is_numeric($this->finalWords[$key])) {
                $this->finalWords[$key] = max($value, $this->finalWords[$key]);
            }
        }
    }

    private function filterOutNumbersFromArray($arrayToFilter): array
    {
        foreach ($arrayToFilter as $key => $wordToFilter){
            $arrayToFilter[$key] = $this->removeNumbersFromString($wordToFilter);
        }

        return $arrayToFilter;
    }

    private function removeNumbersFromString(string $word): string
    {
        return preg_replace("/[^a-zA-Z.]/", "", $word);
    }

    private function isFirstSyllable(string $word): bool
    {
        return str_starts_with($word, '.');
    }

    private function isLastSyllable(string $word): bool
    {
        return str_ends_with($word, '.');
    }

    private function addHyphensAndWhitespaces(): string
    {
        foreach ($this->finalWords as $key => $value) {
            if (is_numeric($value)) {
                if ($value % 2 === 0) {
                    $this->finalWords[$key] = " ";
                } else {
                    $this->finalWords[$key] = "-";
                }
            }
        }

        return implode($this->finalWords);
    }

    private function removeTrailingSymbols(string $finalWord): string
    {
        $finalWord = str_replace('.', '', $finalWord);
        $finalWord = str_replace(' ', '', $finalWord);
        $finalWord = ltrim($finalWord, '-');

        return rtrim($finalWord, '-');
    }

    private function clearArrays(): void
    {
        $this->selectedSyllables = [];
        $this->finalWords = [];
        $this->doubledIndexWords = [];
    }
}
