<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\HyphenatedWord;
use PDO;

class HyphenatedWordRepository
{
    public function __construct(
        private readonly PDO $connection
    ){
    }

    public function findHyphenatedWordById(int $foreignKey): ?HyphenatedWord
    {
        $stmt = $this->connection->prepare("SELECT * FROM hyphenated_words WHERE word_id = :word_id");
        $stmt->execute(['word_id' => $foreignKey]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return new HyphenatedWord($result['id'], $result['text'], $result['word_id']);
    }

    public function checkIfHyphenatedWordExistsDb(int $wordId): bool
    {
        $stmt = $this->connection->prepare("SELECT * FROM hyphenated_words WHERE word_id = :word_id");
        $stmt->execute(['word_id' => $wordId]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return !empty($result);
    }

    public function insertHyphenatedWord(string $word, int $foreignKey): int
    {
        $wordExists = $this->checkIfHyphenatedWordExistsDb($foreignKey);

        if (!$wordExists)
        {
            $stmt = $this->connection->prepare("INSERT INTO hyphenated_words (text, word_id) VALUES (?,?)");
            $stmt->execute([$word, $foreignKey]);
        }

        return (int)($this->connection->lastInsertId());
    }

    public function insertManyHyphenatedWords(array $words): void
    {
        foreach ($words as $key => $word) {
            $stmt = $this->connection->prepare("INSERT INTO hyphenated_words (text, word_id) VALUES (?,?)");
            $stmt->execute([$word, $key]);
        }
    }

    public function insertHyphenatedWordAndSyllableIds(array $selectedSyllableIds, int $hyphenatedWordId): void
    {
        $stmt = $this->connection->prepare("INSERT INTO selected_Syllables_hyphenated_Words (hyphenated_word_id, selected_syllable_id) VALUES (?,?)");

        foreach ($selectedSyllableIds as $syllableId) {
            $stmt->execute([$hyphenatedWordId, $syllableId]);
        }
    }
}
