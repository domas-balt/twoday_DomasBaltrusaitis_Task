<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\FileService;

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
