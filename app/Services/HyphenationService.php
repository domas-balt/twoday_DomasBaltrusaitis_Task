<?php

namespace App\Services;

class HyphenationService {

    private String $wordToHyphenate;
    private String $initialWord;
    private array $hyphenArray;
    private array $expandedLetterArray;
    private array $finalHyphenArray;
    private array $finalWordArray;
    private string $finalProcessedWord;

    public function __construct(String $wordToHyphenate, array $hyphenArray)
    {
        $this->hyphenArray = $hyphenArray;
        $this->wordToHyphenate = $wordToHyphenate;
        $this->initialWord = $wordToHyphenate;
        $this->finalHyphenArray = [];
        $this->finalWordArray = [];
        $this->finalProcessedWord = '';
        $this->expandedLetterArray = [];
    }

    public function FindSyllables() : void
    {
        $arrayWithoutNumbers = $this->FilterOutNumbersFromArray($this->hyphenArray);
        $fullPatternArray = [];

        // FIND CORRECT SYLLABLES
        foreach ($arrayWithoutNumbers as $key => $syllable) {
            if ($this->IsFirstSyllable($syllable) && str_starts_with($this->wordToHyphenate, substr($syllable, 1))) {
                $this->finalHyphenArray[$key] = $syllable;
            }
            if (!$this->IsFirstSyllable($syllable) && !$this->IsLastSyllable($syllable) && str_contains($this->wordToHyphenate, $syllable)) {
                $this->finalHyphenArray[$key] = $syllable;
            }
            if ($this->IsLastSyllable($syllable) && str_ends_with($this->wordToHyphenate, substr($syllable, 0, -1))) {
                $this->finalHyphenArray[$key] = $syllable;
            }
        }

        echo "Initial selected syllables:\n";
        foreach ($this->finalHyphenArray as $key => $value) {
            print($this->hyphenArray[$key]);
            $fullPatternArray[$key] = $this->hyphenArray[$key];
        }

        $this->wordToHyphenate = "." . $this->wordToHyphenate . ".";
        $wordCharacterArray = str_split($this->wordToHyphenate);
        $preCleanUpCharacterArray = [];

        foreach ($this->finalHyphenArray as $key => $pattern) {
            $patternWithoutNumbers = str_split($this->RemoveNumbersFromString($pattern));
            $fullPatternChars = str_split(str_replace("\n","",$fullPatternArray[$key]));
            $successfulMatchCount = 0;
            $comparisonBuffer = 0;
            $currentIndex = 0;
            $patternPositions = [];

            while ($successfulMatchCount < count($patternWithoutNumbers)) {
                if ($wordCharacterArray[$comparisonBuffer + $currentIndex] !== $patternWithoutNumbers[$currentIndex]) {
                    $successfulMatchCount = 0;
                    $patternPositions = [];
                }

                if($wordCharacterArray[$comparisonBuffer + $currentIndex] === $patternWithoutNumbers[$currentIndex]){
                    $successfulMatchCount++;
                    $patternPositions[$comparisonBuffer + $currentIndex] = $patternWithoutNumbers[$currentIndex];
                }

                $currentIndex++;

                if ($currentIndex >= count($patternWithoutNumbers)) {
                    $comparisonBuffer++;
                    $currentIndex = 0;
                }

                if ($successfulMatchCount === count($patternWithoutNumbers)) {
                    self::BuildWordWithNumbers($patternPositions, $fullPatternChars);
                    break;
                }
            }
        }
        ksort($this->finalWordArray);
        ksort($this->expandedLetterArray);
        $wordToHyphenateSplit = str_split($this->wordToHyphenate);
        $wordToHyphenateExpandedArray = [];
        foreach ($wordToHyphenateSplit as $key => $wordToHyphenateChar) {
            $wordToHyphenateExpandedArray[$key * 2] = $wordToHyphenateChar;
        }

        $this->finalWordArray = $this->finalWordArray + $wordToHyphenateExpandedArray;
        ksort($this->finalWordArray);
        print_r($this->finalWordArray);
        self::FinalProcessing();
    }


    private function FilterOutNumbersFromArray($arrayToFilter) : array{
        foreach ($arrayToFilter as $key => $wordToFilter){
            $arrayToFilter[$key] = self::RemoveNumbersFromString($wordToFilter);
        }

        return $arrayToFilter;
    }

    private function RemoveNumbersFromString(String $word) : String{
        return preg_replace("/[^a-zA-Z.]/", "", $word);
    }

    private function IsFirstSyllable(String $word) : bool{
        if ((strpos($word, ".") === 0)) {
            return true;
        }
        return false;
    }

    private function IsLastSyllable(String $word) : bool{
        if (str_ends_with($word, ".")) {
            return true;
        }
        return false;
    }

    private function BuildWordWithNumbers(Array $patternWithCharPositions, Array $fullPattern) : void{

        $patternWithCharPositionsExpanded = [];
        foreach ($patternWithCharPositions as $key => $patternCharNoNumber) {
            $patternWithCharPositionsExpanded[$key * 2] = $patternCharNoNumber;
            $this->expandedLetterArray[$key * 2] = $patternCharNoNumber;
        }

        $iterationDoubleKey = array_key_first($patternWithCharPositionsExpanded);

        for ($i = 0; $i < count($fullPattern); $i++) {
            if (is_numeric($fullPattern[$i])) {
                $patternWithCharPositionsExpanded[$iterationDoubleKey - 1] = $fullPattern[$i];
                continue;
            }

            $iterationDoubleKey = $iterationDoubleKey + 2;
        }

        ksort($patternWithCharPositionsExpanded);

        foreach ($patternWithCharPositionsExpanded as $key => $value) {
            if (!isset($this->finalWordArray[$key])){
                $this->finalWordArray[$key] = $value;
            }

            if (isset($this->finalWordArray[$key]) && is_numeric($value) && is_numeric($this->finalWordArray[$key])){
                $this->finalWordArray[$key] = max($value, $this->finalWordArray[$key]);
            }
        }
    }

    private function FinalProcessing() : void{
        foreach ($this->finalWordArray as $key => $value) {
            if (is_numeric($value)) {
                if ($value % 2 === 0) {
                    $this->finalWordArray[$key] = " ";
                }
                else {
                    $this->finalWordArray[$key] = "-";
                }
            }
        }

        $this->finalProcessedWord = implode($this->finalWordArray);

        $this->finalProcessedWord = str_replace(".", "", $this->finalProcessedWord);
        $this->finalProcessedWord = str_replace(" ", "", $this->finalProcessedWord);
        $this->finalProcessedWord = ltrim($this->finalProcessedWord, "-");
        $this->finalProcessedWord = rtrim($this->finalProcessedWord, "-");

        print_r($this->finalProcessedWord . "\n");
    }

    public function GetFinalWord() : string{
        return $this->finalProcessedWord;
    }
}