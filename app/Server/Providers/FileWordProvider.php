<?php

declare(strict_types=1);

namespace App\Server\Providers;

use App\Server\Services\FileService;

readonly class FileWordProvider implements WordProviderInterface
{
    public function __construct(
        private string $filename
    ) {
    }

    public function getWords(): array
    {
        return FileService::readDataFromFile($this->filename);
    }
}
