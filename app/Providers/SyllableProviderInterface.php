<?php

declare(strict_types=1);

namespace App\Providers;

use App\Entities\Syllable;

interface SyllableProviderInterface
{
    /**
     * @return Syllable[]
     */
    public function getSyllables(): array;
}
