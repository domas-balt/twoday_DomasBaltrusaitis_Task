<?php

declare(strict_types=1);

namespace App\Server\Services;

interface HyphenationServiceInterface
{
    public function hyphenateWords(array $words): array;
    public function getSyllables(): array;
}
