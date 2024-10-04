<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\WordRepository;

class DatabaseWordProvider implements WordProviderInterface
{
    public function __construct(
        private readonly WordRepository $wordRepository
    ){
    }

    public function getWords(): array
    {
        return $this->wordRepository->getAllWords();
    }
}
