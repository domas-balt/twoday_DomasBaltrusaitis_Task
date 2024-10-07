<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\HyphenatedWord;
use App\Entities\SelectedSyllable;

interface HyphenationManagementInterface
{
    /**
     * @param array $words
     * @return array{
     *     'hyphenatedWord': HyphenatedWord,
     *     'selectedSyllables': SelectedSyllable[]
     * }
     */
    public function manageHyphenation(array $words): array;
}
