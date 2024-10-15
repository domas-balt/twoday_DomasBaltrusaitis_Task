<?php

declare(strict_types=1);

namespace App\Server\Providers;

use App\Server\Repositories\WordRepository;

class DatabaseWordProvider implements WordProviderInterface
{
    public function __construct(
        private readonly WordRepository $wordRepository
    ) {
    }

    public function getWords(): array
    {
        return $this->wordRepository->getAllWords(true);
    }
}
