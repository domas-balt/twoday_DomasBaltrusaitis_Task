<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use PDOException;

class WordRepository
{
    public function __construct(
        private readonly PDO $connection
    ){

    }

    public function loadWordsFromFileToDb(string $fileName): void
    {
        $path = dirname(__DIR__) . $fileName;

        if (!file_exists($path)) {
            throw new \InvalidArgumentException("File not found with path {$path} and file name {$fileName}!");
        }

        try {
            $stmt = $this->connection->prepare(
                "LOAD DATA LOCAL INFILE :path INTO TABLE words
                FIELDS TERMINATED BY ''
                (text)"
            );

            $stmt->execute(['path' => $path]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function insertWord(string $word): string
    {
        $stmt = $this->connection->prepare("INSERT INTO words (text) VALUES (:words_text)");
        $stmt->execute(['words_text' => $word]);

        return $this->connection->lastInsertId();
    }

    public function insertHyphenatedWord(string $word, string $foreignKey): void
    {
        $stmt = $this->connection->prepare("INSERT INTO hyphenated_words (text, word_id) VALUES (?,?)");
        $stmt->execute([$word, $foreignKey]);
    }

    public function insertHyphenatedWordAndSyllableIds(array $syllableIds, int $hyphenatedWordId): void
    {
        $stmt = $this->connection->prepare("INSERT INTO selected_Syllables_hyphenated_Words (hyphenated_word_id, selected_syllable_id) VALUES (?,?)");

        foreach ($syllableIds as $syllableId) {
            $stmt->execute([$hyphenatedWordId, $syllableId]);
        }
    }

    public function checkIfWordExistsDb(string $word): bool
    {
        $stmt = $this->connection->prepare("SELECT * FROM words WHERE text = :text");
        $stmt->execute(['text' => $word]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return !empty($result);
    }

    public function findHyphenatedWordById(int $foreignKey): mixed
    {
        $stmt = $this->connection->prepare("SELECT * FROM hyphenated_words WHERE word_id = :word_id");
        $stmt->execute(['word_id' => $foreignKey]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findHyphenatedWord(string $word): mixed
    {
        $stmt = $this->connection->prepare("SELECT * FROM words WHERE text = :text");
        $stmt->execute(['text' => $word]);

        $wordResult = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->findHyphenatedWordById($wordResult['id']);
    }

    public function clearWordTable(): void
    {
        $stmt = $this->connection->prepare('DELETE FROM words');
        $stmt->execute();
    }
}
