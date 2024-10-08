<?php

declare(strict_types=1);

namespace App\Providers;

use App\Entities\Word;
use App\Services\UserInputService;

readonly class CliWordProvider implements WordProviderInterface
{
    public function __construct(
        private UserInputService $userInputService
    ) {
    }

    public function getWords(): array
    {
        return [$this->userInputService->readWordToHyphenate()];
    }
}
