<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\SelectedSyllable;

readonly class SelectedSyllableRepository
{
    public function __construct(
        private \PDO $connection
    ) {
    }

    /**
     * @param SelectedSyllable[] $selectedSyllables
     */
    public function insertSelectedSyllables(array $selectedSyllables): array
    {
        $selectedSyllableIds = [];

        $query = $this->connection->prepare('INSERT INTO selected_syllables (text) VALUES (:selected_syllable_text)');

        foreach ($selectedSyllables as $selectedSyllable) {
            $query->execute(['selected_syllable_text' => $selectedSyllable->getText()]);
            $selectedSyllableIds[] = $this->connection->lastInsertId();
        }

        return $selectedSyllableIds;
    }

    public function insertSelectedSyllable(string $text): int
    {
        $query = $this->connection->prepare('INSERT INTO selected_syllables (text) VALUES (:text)');

        $query->execute(['text' => $text]);

        return (int) $this->connection->lastInsertId();
    }
}
