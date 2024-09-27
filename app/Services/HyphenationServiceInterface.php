<?php

namespace App\Services;

interface HyphenationServiceInterface
{
    public function hyphenateWord(array $wordsArray): array;
}