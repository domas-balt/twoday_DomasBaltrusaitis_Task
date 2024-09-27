<?php
declare(strict_types=1);

namespace App\Services;

interface HyphenationServiceInterface
{
    public function hyphenateWord(array $wordsArray): array;
}