<?php

namespace App\Services;

class ResultVisualizationService {
    public static function VisualizeResults(string $finalWord){
        $finalWord = self::ProcessFinalWord($finalWord);
        print_r("Final word:\n" . $finalWord . "\n");
    }

    private static function ProcessFinalWord(string $finalWord) : string{
        $finalWord = str_replace(".", "", $finalWord);
        $finalWord = str_replace(" ", "", $finalWord);
        $finalWord = ltrim($finalWord, "-");
        $finalWord = rtrim($finalWord, "-");
        return $finalWord;
    }
}