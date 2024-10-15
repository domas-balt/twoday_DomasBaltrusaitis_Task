<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\QueryBuilder\SqlQueryBuilder;
use App\Entities\SelectedSyllable;

readonly class SelectedSyllableRepository
{
    public function __construct(
        private SqlQueryBuilder $sqlQueryBuilder,
        private \PDO $connection
    ) {
    }

    /**
     * @param SelectedSyllable[] $selectedSyllables
     */
    public function insertSelectedSyllables(array $selectedSyllables): array
    {
        $selectedSyllableIds = [];

        $queryString = $this->sqlQueryBuilder
            ->insert('selected_syllables', ['text'])
            ->values(['(:selected_syllable_text)'])
            ->getSql();

        $query = $this->connection->prepare($queryString);

        foreach ($selectedSyllables as $selectedSyllable) {
            $query->execute(['selected_syllable_text' => $selectedSyllable->getText()]);
            $selectedSyllableIds[] = $this->connection->lastInsertId();
        }

        return $selectedSyllableIds;
    }

    public function insertSelectedSyllable(string $text): int
    {
        $queryString = $this->sqlQueryBuilder
            ->insert('selected_syllables', ['text'])
            ->values(['(:text)'])
            ->getSql();

        $query = $this->connection->prepare($queryString);
        $query->execute(['text' => $text]);

        return (int) $this->connection->lastInsertId();
    }
}
