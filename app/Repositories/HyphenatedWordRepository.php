<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\HyphenatedWord;

class HyphenatedWordRepository
{
    public function __construct(
        private readonly \PDO $connection
    ) {
    }

    public function findHyphenatedWordById(int $foreignKey): ?HyphenatedWord
    {
        $query = $this->connection->prepare("SELECT * FROM hyphenated_words WHERE word_id = :word_id");
        $query->execute(['word_id' => $foreignKey]);

        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return new HyphenatedWord($result['id'], $result['text'], $result['word_id']);
    }

    public function insertHyphenatedWord(string $word, int $foreignKey): int
    {
        $hyphenatedWord = $this->findHyphenatedWordById($foreignKey);

        if ($hyphenatedWord == null) {
            $query = $this->connection->prepare("INSERT INTO hyphenated_words (text, word_id) VALUES (?,?)");
            $query->execute([$word, $foreignKey]);
        }

        return (int) ($this->connection->lastInsertId());
    }

    public function insertHyphenatedWordAndSyllableIds(array $selectedSyllableIds, int $hyphenatedWordId): void
    {
        if (empty($selectedSyllableIds) || $hyphenatedWordId == null) {
            return;
        }

        $data = [];

        foreach ($selectedSyllableIds as $syllableId) {
            $data[] = $hyphenatedWordId;
            $data[] = $syllableId;
        }

        $placeholders = rtrim(str_repeat('(?, ?), ', count($selectedSyllableIds)), ', ');

        $query = $this->connection->prepare("INSERT INTO selected_Syllables_hyphenated_Words (hyphenated_word_id, selected_syllable_id) VALUES {$placeholders}");
        $query->execute($data);
    }
}
