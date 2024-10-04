<?php

declare(strict_types=1);

namespace App\Providers;

use App\Entities\Syllable;
use App\Services\FileService;

class FileSyllableProvider implements SyllableProviderInterface
{
    /**
     * @return Syllable[]
     */
    public function getSyllables(): array
    {
        $syllableFromFile = FileService::readDataFromFile('/var/hyphen.txt');

        $syllables = [];

        foreach ($syllableFromFile as $key => $syllablePattern) {
            $syllables[] = new Syllable($key, $syllablePattern);
        }

        return $syllables;
    }
}
