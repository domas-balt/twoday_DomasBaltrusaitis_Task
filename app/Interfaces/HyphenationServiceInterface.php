<?php

namespace App\Interfaces;

interface HyphenationServiceInterface
{
    public function hyphenateWord(array $wordsArray): array;
}