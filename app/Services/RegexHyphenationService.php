<?php

declare(strict_types=1);

namespace App\Services;

readonly class RegexHyphenationService implements HyphenationServiceInterface
{
    public function __construct(
        private array $syllables,
    ){
    }

    public function hyphenateWords(array $words): array
    {
        $serviceWords = $words;

        foreach ($serviceWords as $key => $word) {
            $wordChars = $this->prepareWord($word);

            foreach ($this->syllables as $syllable) {
                $syllableChars = $this->prepareSyllable($syllable);
                $tempWordChars = $wordChars;
                $matchCount = 0;

                for ($i = 0; $i < count($tempWordChars); $i++) {
                    for ($j = 0; $j < count($syllableChars); $j++) {
                        $addedIndices = $i + $j;

                        if (
                            $addedIndices < count($tempWordChars)
                            && is_numeric($tempWordChars[$addedIndices])
                            && is_numeric($syllableChars[$j])
                        ) {
                            $matchCount++;

                            continue;
                        }

                        if (
                            $addedIndices < count($tempWordChars)
                            && $tempWordChars[$addedIndices] === $syllableChars[$j]
                        ) {
                            $matchCount++;

                            continue;
                        }

                        $matchCount = 0;
                    }

                    if ($matchCount == count($syllableChars)) {
                        $startingIndex = $i;
                        for ($i = 0; $i < count($syllableChars); $i++) {
                            if (is_numeric($wordChars[$startingIndex + $i]) && is_numeric($syllableChars[$i])) {
                                $wordChars[$startingIndex + $i] = max($wordChars[$startingIndex + $i], $syllableChars[$i]);

                                continue;
                            }

                            $wordChars[$startingIndex + $i] = $syllableChars[$i];
                        }

                        break;
                    }
                }
            }
            $finalWord = $this->regexFinalProcessing($wordChars);
            $serviceWords[$key] = $finalWord;
        }

        return $serviceWords;
    }

    private function regexSpreadOutCharacters(string $wordToSpreadOut): string
    {
        return preg_replace("/[A-Za-z](?!\.|\d|$)/", '${0}0', $wordToSpreadOut);
    }

    private function regexSplitString(string $stringToSplit): array
    {
        return preg_split('//', $stringToSplit,-1, PREG_SPLIT_NO_EMPTY);
    }

    private function regexFinalProcessing(array $chars): string
    {
        $finalString = implode('', $chars);
        $finalString = preg_replace('/[02468.]/', '', $finalString);
        $finalString = preg_replace('/[1357]/', '-', $finalString);

        return rtrim($finalString, '-');
    }

    private function prepareSyllable(string $syllable): array
    {
        $syllable = trim($syllable, PHP_EOL);
        $syllable = $this->regexSpreadOutCharacters($syllable);

        return $this->regexSplitString($syllable);
    }

    private function prepareWord(string $word): array
    {
        $word = trim($word, PHP_EOL);
        $word = $this->regexSpreadOutCharacters(".{$word}.");

        return $this->regexSplitString($word);
    }
}
