<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\FileService;

class FileWordProvider implements WordProviderInterface
{
    public function __construct(
        private string $filename
    ){
    }

    public function getWords(): array
    {
        $wordsFromFile = FileService::readDataFromFile($this->filename);

        $words = [];

        foreach ($wordsFromFile as $word) {
            $words[] = $word;
        }

        return $words;
    }
}
