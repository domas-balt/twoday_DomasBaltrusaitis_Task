<?php

declare(strict_types=1);

namespace App\Server\Providers;

use App\Server\Services\UserInputService;

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
