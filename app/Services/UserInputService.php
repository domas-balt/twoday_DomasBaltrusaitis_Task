<?php
declare(strict_types=1);

namespace App\Services;

class UserInputService
{
    public static function readWordToHyphenated(): string
    {
        return readline("Enter the word you want to hyphenate:\n");
    }
}
