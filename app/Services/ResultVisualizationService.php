<?php

namespace App\Services;

class ResultVisualizationService {
    public static function VisualizeResults(string $finalWord) : void{
        $finalWord = self::ProcessFinalWord($finalWord);
        print_r("Final word:\n" . $finalWord . "\n");
    }

    private static function ProcessFinalWord(string $finalWord) : string{
        //        print_r(implode('', $this->finalWordArray) . "\n");
        $finalWord = str_replace(".", "", $finalWord);
        $finalWord = str_replace(" ", "", $finalWord);
        $finalWord = ltrim($finalWord, "-");
        $finalWord = rtrim($finalWord, "-");
        return $finalWord;
    }
}