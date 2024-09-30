<?php

declare(strict_types=1);

namespace App\Services;

class HyphenationService implements HyphenationServiceInterface
{
    private array $doubledIndexWords = [];
    private array $selectedSyllables = [];
    private array $finalWords = [];

    public function __construct(
        private readonly array $syllableArray
    ){
    }

    public function hyphenateWords(array $words): array
    {
        foreach ($words as $key => $word) {
            $patternsWithNumbers = $this->findUsableSyllables($word);
            $word = $this->findSyllablePositionsInWord($word, $patternsWithNumbers);
            $this->mergeSyllablesAndWordPositionArrays($word);
            $finalProcessedWord = $this->addHyphensAndWhitespaces();

            $words[$key] = $this->removeTrailingSymbols($finalProcessedWord);

            $this->clearArrays();
        }

        return $words;
    }

    private function findUsableSyllables(string $word): array
    {
        $arrayWithoutNumbers = $this->FilterOutNumbersFromArray($this->syllableArray);
        $patternsWithNumbers = [];

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
            $patternsWithNumbers[$key] = $this->syllableArray[$key];
        }

        return $patternsWithNumbers;
    }

    private function findSyllablePositionsInWord(string $word, array $patternsWithNumbers): string
    {
        $word = "." . $word . ".";
        $wordCharacterArray = str_split($word);

        foreach ($this->selectedSyllables as $key => $pattern) {
            $patternWithoutNumbers = str_split($this->removeNumbersFromString($pattern));
            $fullPatternChars = str_split(str_replace("\n","",$patternsWithNumbers[$key]));

            $successfulMatchCount = 0;
            $comparisonBuffer = 0;
            $currentIndex = 0;

            $patternPositions = [];

            while ($successfulMatchCount < count($patternWithoutNumbers)) {
                if ($wordCharacterArray[$comparisonBuffer + $currentIndex]
                    !== $patternWithoutNumbers[$currentIndex]
                ) {
                    $successfulMatchCount = 0;
                    $patternPositions = [];
                }

                if ($wordCharacterArray[$comparisonBuffer + $currentIndex]
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
        $wordToHyphenateExpandedArray = [];

        foreach ($wordToHyphenateSplit as $key => $wordToHyphenateChar) {
            $wordToHyphenateExpandedArray[$key * 2] = $wordToHyphenateChar;
        }

        $this->finalWords = $this->finalWords + $wordToHyphenateExpandedArray;
        ksort($this->finalWords);
    }

    private function buildWordWithNumbers(Array $patternWithCharPositions, Array $fullPattern): void
    {
        $doubledIndexPatternArray = [];

        foreach ($patternWithCharPositions as $key => $patternCharNoNumber) {
            $doubledIndexPatternArray[$key * 2] = $patternCharNoNumber;
            $this->doubledIndexWords[$key * 2] = $patternCharNoNumber;
        }

        $iterationDoubleKey = array_key_first($doubledIndexPatternArray);

        for ($i = 0; $i < count($fullPattern); $i++) {
            if (is_numeric($fullPattern[$i])) {
                $doubledIndexPatternArray[$iterationDoubleKey - 1] = $fullPattern[$i];

                continue;
            }

            $iterationDoubleKey = $iterationDoubleKey + 2;
        }

        ksort($doubledIndexPatternArray);

        foreach ($doubledIndexPatternArray as $key => $value) {
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
        if ((str_starts_with($word, "."))) {

            return true;
        }

        return false;
    }

    private function isLastSyllable(string $word): bool
    {
        if (str_ends_with($word, ".")) {

            return true;
        }

        return false;
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
        $finalWord = str_replace(".", "", $finalWord);
        $finalWord = str_replace(" ", "", $finalWord);
        $finalWord = ltrim($finalWord, "-");

        return rtrim($finalWord, "-");
    }

    private function clearArrays(): void
    {
        $this->selectedSyllables = [];
        $this->finalWords = [];
        $this->doubledIndexWords = [];
    }
}
