<?php

declare(strict_types=1);

namespace App\Services;

class HyphenationService implements HyphenationServiceInterface
{
    private array $doubledIndexWordArray = [];
    private array $selectedSyllableArray = [];
    private array $finalWordArray = [];
    private string $finalProcessedWord = '';

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
            $this->addHyphensAndWhitespaces();

            $finalProcessedWord = $this->removeTrailingSymbols($this->finalProcessedWord);
            $words[$key] = $finalProcessedWord;

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
                $this->selectedSyllableArray[$key] = $syllable;
            }

            if (
                !$this->isFirstSyllable($syllable)
                && !$this->isLastSyllable($syllable)
                && str_contains($word, $syllable)
            ) {
                $this->selectedSyllableArray[$key] = $syllable;
            }

            if ($this->isLastSyllable($syllable)
                && str_ends_with($word, substr($syllable, 0, -1))
            ) {
                $this->selectedSyllableArray[$key] = $syllable;
            }
        }

        foreach ($this->selectedSyllableArray as $key => $value) {
            $patternsWithNumbers[$key] = $this->syllableArray[$key];
        }

        return $patternsWithNumbers;
    }

    private function findSyllablePositionsInWord(string $word, array $patternsWithNumbers): string
    {
        $word = "." . $word . ".";
        $wordCharacterArray = str_split($word);

        foreach ($this->selectedSyllableArray as $key => $pattern) {
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
        ksort($this->finalWordArray);
        ksort($this->doubledIndexWordArray);
        
        $wordToHyphenateSplit = str_split($word);
        $wordToHyphenateExpandedArray = [];

        foreach ($wordToHyphenateSplit as $key => $wordToHyphenateChar) {
            $wordToHyphenateExpandedArray[$key * 2] = $wordToHyphenateChar;
        }

        $this->finalWordArray = $this->finalWordArray + $wordToHyphenateExpandedArray;
        ksort($this->finalWordArray);
    }

    private function buildWordWithNumbers(Array $patternWithCharPositions, Array $fullPattern): void
    {
        $doubledIndexPatternArray = [];

        foreach ($patternWithCharPositions as $key => $patternCharNoNumber) {
            $doubledIndexPatternArray[$key * 2] = $patternCharNoNumber;
            $this->doubledIndexWordArray[$key * 2] = $patternCharNoNumber;
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
            if (!isset($this->finalWordArray[$key])){
                $this->finalWordArray[$key] = $value;
            }

            if (isset($this->finalWordArray[$key]) &&
                is_numeric($value) &&
                is_numeric($this->finalWordArray[$key])) {
                $this->finalWordArray[$key] = max($value, $this->finalWordArray[$key]);
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

    private function addHyphensAndWhitespaces(): void
    {
        foreach ($this->finalWordArray as $key => $value) {
            if (is_numeric($value)) {
                if ($value % 2 === 0) {
                    $this->finalWordArray[$key] = " ";
                } else {
                    $this->finalWordArray[$key] = "-";
                }
            }
        }

        $this->finalProcessedWord = implode($this->finalWordArray);
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
        $this->selectedSyllableArray = [];
        $this->finalWordArray = [];
        $this->doubledIndexWordArray = [];
    }
}
