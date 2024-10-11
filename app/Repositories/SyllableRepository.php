<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\QueryBuilder\SqlQueryBuilder;
use App\Entities\SelectedSyllable;
use App\Entities\Syllable;
use App\Logger\Logger;

class SyllableRepository
{
    public function __construct(
        private SqlQueryBuilder $sqlQueryBuilder,
        private readonly \PDO $connection,
        private readonly Logger $logger,
    ) {
    }

    public function getAllSyllables(): array
    {
        $queryString = $this->sqlQueryBuilder
            ->select('syllables', ['*'])
            ->getSql();

        $query = $this->connection->query($queryString);
        $syllables = $query->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(
            fn (array $syllable): Syllable => new Syllable($syllable['id'], $syllable['pattern']),
            $syllables,
        );
    }

    public function insertManySyllables(array $syllables): void
    {
        if (empty($syllables)) {
            return;
        }

        $placeholders = rtrim(str_repeat('(?), ', count($syllables)), ', ');

        $queryString = $this->sqlQueryBuilder
            ->insert('syllables', ['pattern'])
            ->values([$placeholders])
            ->getSql();

        $query = $this->connection->prepare($queryString);
        $query->execute($syllables);
    }

    public function getAllSyllablesByHyphenatedWordId(int $hyphenatedWordId): array
    {
        $queryString = $this->sqlQueryBuilder
            ->select('hyphenated_words_selected_syllables', ['*'])
            ->where('hyphenated_word_id', ':hyphenated_word_id')
            ->getSql();

        $query = $this->connection->prepare($queryString);
        $query->execute([':hyphenated_word_id' => $hyphenatedWordId]);

        $wordSyllableRows = $query->fetchAll(\PDO::FETCH_ASSOC);

        $selectedSyllableIds = [];

        foreach ($wordSyllableRows as $syllableRow) {
            $selectedSyllableIds[] = $syllableRow['selected_syllable_id'];
        }

        return $this->getAllSyllablesByIds($selectedSyllableIds);
    }

    public function clearSyllableTable(): void
    {
        $queryString = $this->sqlQueryBuilder
            ->delete('syllables')
            ->getSql();

        $query = $this->connection->prepare($queryString);
        $query->execute();
    }

    /**
     * @return SelectedSyllable[]
     */
    public function getAllSyllablesByIds(array $selectedSyllableIds): array
    {
        $placeholders = rtrim(str_repeat('?, ', count($selectedSyllableIds)), ', ');

        $queryString = $this->sqlQueryBuilder
            ->select('selected_syllables', ['*'])
            ->where('id', $placeholders, 'IN')
            ->getSql();


        $query = $this->connection->prepare($queryString);
        $query->execute($selectedSyllableIds);

        $result = $query->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(
            fn (array $syllable): SelectedSyllable => new SelectedSyllable($syllable['id'], $syllable['text']),
            $result,
        );
    }
}
