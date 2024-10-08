<?php

declare(strict_types=1);

namespace App\Services;

use JetBrains\PhpStorm\ArrayShape;

interface HyphenationManagementInterface
{
    #[ArrayShape([
        'hyphenated_word' => 'App\Entities\HyphenatedWord',
        'syllables' => 'App\Entities\SelectedSyllable[]',
    ])]
    public function manageHyphenation(array $words): array;
}
