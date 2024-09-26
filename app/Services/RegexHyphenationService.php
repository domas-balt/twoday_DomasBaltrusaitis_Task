<?php
declare(strict_types=1);

namespace App\Services;

use App\Interfaces\IHyphenationService;

class RegexHyphenationService implements IHyphenationService
{
    public function __construct(
        private readonly array $syllableArray,
        private array $wordsArray,
    ){}

    public function hyphenateWord() : array
    {
        foreach ($this->wordsArray as $key => $word) {
            $word = rtrim($word, "\n");
            $word = "." . $word . ".";
            $word = $this->regexSpreadOutCharacters($word);
            $wordCharArray = $this->regexSplitString($word);

            foreach ($this->syllableArray as $syllable) {
                $syllable = rtrim($syllable, "\n");
                $syllable = $this->regexSpreadOutCharacters($syllable);
                $syllableCharArray = $this->regexSplitString($syllable);

                $tempWordCharArray = $wordCharArray;
                $matchCount = 0;

                for ($i = 0; $i < count($tempWordCharArray); $i++) {
                    for ($j = 0; $j < count($syllableCharArray); $j++) {
                        if (($i + $j) < count($tempWordCharArray) && is_numeric($tempWordCharArray[$i + $j]) && is_numeric($syllableCharArray[$j])) {
                            $matchCount++;

                            continue;
                        }

                        if (($i + $j) < count($tempWordCharArray) && $tempWordCharArray[$i + $j] == $syllableCharArray[$j]) {
                            $matchCount++;

                            continue;
                        }

                        $matchCount = 0;
                    }

                    if ($matchCount == count($syllableCharArray)) {
                        $startingIndex = $i;
                        for ($i = 0; $i < count($syllableCharArray); $i++) {
                            $wordCharArray[$startingIndex + $i] = $syllableCharArray[$i];
                        }
                        break;
                    }
                }
            }
            $finalWord = $this->regexFinalProcessing($wordCharArray);
            print($finalWord . "\n");
            $this->wordsArray[$key] = $finalWord;
        }
        return $this->wordsArray;
    }

    private function regexSpreadOutCharacters(string $wordToSpreadOut) : string
    {
        return preg_replace("/[A-Za-z](?!\.|\d|$)/", '${0}0', $wordToSpreadOut);
    }

    private function regexSplitString(string $stringToSplit) : array
    {
        return preg_split('//', $stringToSplit,-1, PREG_SPLIT_NO_EMPTY);
    }

    private function regexFinalProcessing(array $charArray) : string
    {
        $finalString = implode('', $charArray);
        $finalString = preg_replace("/[02468.]/", "", $finalString);
        $finalString = preg_replace("/[1357](?!\.|\d|$)/", "-", $finalString);

        return $finalString;
    }

}