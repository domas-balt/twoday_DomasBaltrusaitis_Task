<?php

declare(strict_types=1);

namespace App\Server\Providers;

use App\Server\Entities\Syllable;

interface SyllableProviderInterface
{
    /**
     * @return Syllable[]
     */
    public function getSyllables(): array;
}
