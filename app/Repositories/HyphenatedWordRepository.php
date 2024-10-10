<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\QueryBuilder\SqlQueryBuilder;
use App\Entities\HyphenatedWord;

class HyphenatedWordRepository
{
    public function __construct(
        private SqlQueryBuilder $sqlQueryBuilder,
        private readonly \PDO $connection
    ) {
    }

    public function findHyphenatedWordById(int $foreignKey): ?HyphenatedWord
    {
        $queryString = $this->sqlQueryBuilder
            ->select('hyphenated_words', ['*'])
            ->where('word_id', ':word_id')
            ->getSql();

        $query = $this->connection->prepare($queryString);
        $query->execute(['word_id' => $foreignKey]);

        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return new HyphenatedWord($result['id'], $result['text'], $result['word_id']);
    }

    public function insertHyphenatedWord(string $word, int $wordId): int
    {
        $hyphenatedWord = $this->findHyphenatedWordById($wordId);

        if ($hyphenatedWord == null) {
            $queryString = $this->sqlQueryBuilder
                ->insert('hyphenated_words', ['text', 'word_id'])
                ->values(['(?, ?)'])
                ->getSql();

            $query = $this->connection->prepare($queryString);
            $query->execute([$wordId, $wordId]);
        }

        return (int) ($this->connection->lastInsertId());
    }

    public function insertHyphenatedWordAndSyllableIds(array $selectedSyllableIds, int $hyphenatedWordId): void
    {
        if (empty($selectedSyllableIds)) {
            return;
        }

        $data = [];

        foreach ($selectedSyllableIds as $syllableId) {
            $data[] = $hyphenatedWordId;
            $data[] = $syllableId;
        }

        $placeholders = rtrim(str_repeat('(?, ?), ', count($selectedSyllableIds)), ', ');

        $queryString = $this->sqlQueryBuilder
            ->insert('hyphenated_words_selected_syllables', ['hyphenated_word_id', 'selected_syllable_id'])
            ->values([$placeholders])
            ->getSql();

        $query = $this->connection->prepare($queryString);
        $query->execute($data);
    }
}
