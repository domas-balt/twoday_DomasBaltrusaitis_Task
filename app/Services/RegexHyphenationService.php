<?php
declare(strict_types=1);

namespace App\Services;

readonly class RegexHyphenationService implements HyphenationServiceInterface
{
    public function __construct(
        private array $syllableArray,
    ){
    }

    public function hyphenateWord(array $wordsArray): array
    {
        $serviceWordArray = $wordsArray;

        foreach ($serviceWordArray as $key => $word) {
            $wordCharArray = $this->prepareWord($word);

            foreach ($this->syllableArray as $syllable) {
                $syllableCharArray = $this->prepareSyllable($syllable);
                $tempWordCharArray = $wordCharArray;
                $matchCount = 0;

                for ($i = 0; $i < count($tempWordCharArray); $i++) {
                    for ($j = 0; $j < count($syllableCharArray); $j++) {
                        if (($i + $j) < count($tempWordCharArray)
                            && is_numeric($tempWordCharArray[$i + $j])
                            && is_numeric($syllableCharArray[$j])
                        ) {
                            $matchCount++;

                            continue;
                        }

                        if (($i + $j) < count($tempWordCharArray)
                            && $tempWordCharArray[$i + $j]
                            == $syllableCharArray[$j]
                        ) {
                            $matchCount++;

                            continue;
                        }

                        $matchCount = 0;
                    }

                    if ($matchCount == count($syllableCharArray)) {
                        $startingIndex = $i;
                        for ($i = 0; $i < count($syllableCharArray); $i++) {
                            if (is_numeric($wordCharArray[$startingIndex + $i]) && is_numeric($syllableCharArray[$i])) {
                                $wordCharArray[$startingIndex + $i] = max($wordCharArray[$startingIndex + $i], $syllableCharArray[$i]);

                                continue;
                            }

                            $wordCharArray[$startingIndex + $i] = $syllableCharArray[$i];
                        }

                        break;
                    }
                }
            }
            $finalWord = $this->regexFinalProcessing($wordCharArray);
            $serviceWordArray[$key] = $finalWord;
        }

        return $serviceWordArray;
    }

    private function regexSpreadOutCharacters(string $wordToSpreadOut): string
    {
        return preg_replace("/[A-Za-z](?!\.|\d|$)/", '${0}0', $wordToSpreadOut);
    }

    private function regexSplitString(string $stringToSplit): array
    {
        return preg_split('//', $stringToSplit,-1, PREG_SPLIT_NO_EMPTY);
    }

    private function regexFinalProcessing(array $charArray): string
    {
        $finalString = implode('', $charArray);
        $finalString = preg_replace("/[02468.]/", "", $finalString);
        $finalString = preg_replace("/[1357]/", "-", $finalString);

        return rtrim($finalString, "-");;
    }

    private function prepareSyllable(string $syllable): array
    {
        $syllable = rtrim($syllable, "\n");
        $syllable = $this->regexSpreadOutCharacters($syllable);

        return $this->regexSplitString($syllable);
    }

    private function prepareWord(string $word): array
    {
        $word = rtrim($word, "\n");
        $word = "." . $word . ".";
        $word = $this->regexSpreadOutCharacters($word);

        return $this->regexSplitString($word);
    }
}