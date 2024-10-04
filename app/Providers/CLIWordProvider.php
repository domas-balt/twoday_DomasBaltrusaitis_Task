<?php

declare(strict_types=1);

namespace App\Providers;

use App\Entities\Word;
use App\Services\UserInputService;

class CLIWordProvider implements WordProviderInterface
{
    public function __construct(
        private readonly UserInputService $userInputService
    ){
    }

    public function getWords(): array
    {
        $words[] = $this->userInputService->readWordToHyphenate();

        return $words;
    }
}
