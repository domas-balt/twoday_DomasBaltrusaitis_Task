<?php

namespace App\Services;

class HyphenationService {

    private String $wordToHyphenate;
    private String $initialWord;
    private array $hyphenArray;
    private array $finalHyphenArray;

    public function __construct(String $wordToHyphenate, array $hyphenArray)
    {
        $this->hyphenArray = $hyphenArray;
        $this->wordToHyphenate = $wordToHyphenate;
        $this->initialWord = $wordToHyphenate;
        $this->finalHyphenArray = [];
    }

    public function FindSyllables() : void
    {
        $arrayWithoutNumbers = $this->FilterOutNumbersFromArray($this->hyphenArray);

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
        }

        $this->wordToHyphenate = "." . $this->wordToHyphenate . ".";
        $wordCharacterArray = str_split($this->wordToHyphenate);
        $preCleanUpCharacterArray = [];

        foreach ($this->finalHyphenArray as $pattern) {
            $patternWithoutNumbers = str_split($this->RemoveNumbersFromString($pattern));
            $successfulMatchCount = 0;
            $comparisonBuffer = 0;
            $currentIndex = 0;

            while ($successfulMatchCount < count($patternWithoutNumbers)) {
                if ($wordCharacterArray[$comparisonBuffer + $currentIndex] !== $patternWithoutNumbers[$currentIndex]) {
                    $successfulMatchCount = 0;
                }

                if($wordCharacterArray[$comparisonBuffer + $currentIndex] === $patternWithoutNumbers[$currentIndex]){
                    $successfulMatchCount++;
                }

                $currentIndex++;

                if ($currentIndex >= count($patternWithoutNumbers)) {
                    $comparisonBuffer++;
                    $currentIndex = 0;
                }

                if ($successfulMatchCount === count($patternWithoutNumbers)) {
                    echo "Susimatchino sekmingai sitas: " . $pattern . "\n";
                    break;
                }
            }

//            for($i = 0; $i < count($wordCharacterArray); $i++){
//                for($j = 0; $j < count($patternWithoutNumbers); $j++){
//                    if($wordCharacterArray[$i] == $patternWithoutNumbers[$j]){
//                        $i++;
//                        $successfulMatchCount++;
//                        continue;
//                    }
//                    $successfulMatchCount = 0;
//                }
//            }
        }
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
}