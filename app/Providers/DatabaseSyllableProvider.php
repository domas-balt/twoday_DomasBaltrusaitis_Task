<?php

declare(strict_types=1);

namespace App\Providers;

use App\Entities\Syllable;
use App\Repositories\SyllableRepository;

class DatabaseSyllableProvider implements SyllableProviderInterface
{
    public function __construct(
        private readonly SyllableRepository $syllableRepository
    ) {
    }

    /**
     * @return Syllable[]
     */
    public function getSyllables(): array
    {
        return $this->syllableRepository->getAllSyllables();
    }
}
