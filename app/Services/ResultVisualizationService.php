<?php

namespace App\Services;

class ResultVisualizationService
{
    public static function visualizeResults(string $finalWord) : void
    {
        $finalWord = self::processFinalWord($finalWord);
        print_r("Final word:\n" . $finalWord . "\n");
    }

    private static function processFinalWord(string $finalWord) : string
    {
        $finalWord = str_replace(".", "", $finalWord);
        $finalWord = str_replace(" ", "", $finalWord);
        $finalWord = ltrim($finalWord, "-");
        return rtrim($finalWord, "-");
    }
}