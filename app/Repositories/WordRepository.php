<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Word;
use App\Logger\Logger;
use App\Logger\LogLevel;
use App\Services\FileService;

class WordRepository
{
    public function __construct(
        private readonly \PDO $connection,
        private readonly Logger $logger
    ) {
    }

    public function getAllWords(): array
    {
        $words = [];

        $query = $this->connection->prepare('SELECT words.text, words.id FROM words LEFT JOIN hyphenationdb.hyphenated_words hw on words.id = hw.word_id WHERE hw.word_id IS NULL');
        $query->execute();

        $wordRows = $query->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($wordRows as $wordRow) {
            $words[$wordRow['id']] = $wordRow['text'];
        }

        return $words;
    }

    public function insertWord(string $word): Word
    {
        $query = $this->connection->prepare('INSERT INTO words (text) VALUES (:words_text)');
        $query->execute(['words_text' => $word]);

        $id = $this->connection->lastInsertId();

        return new Word((int) $id, $word);
    }

    public function insertManyWords(array $words): void
    {
        if (empty($words)) {
            return;
        }

        $placeholders = rtrim(str_repeat('(?), ', count($words)), ', ');

        $query = $this->connection->prepare("INSERT INTO words (text) VALUES {$placeholders}");
        $query->execute($words);
    }

    public function checkIfWordExistsDb(string $word): bool
    {
        $query = $this->connection->prepare('SELECT * FROM words WHERE text = :text');
        $query->execute(['text' => $word]);

        $result = $query->fetch(\PDO::FETCH_ASSOC);

        return !empty($result);
    }

    public function findWordByText(string $text): ?Word
    {
        $query = $this->connection->prepare('SELECT * FROM words WHERE text = :text');
        $query->execute(['text' => $text]);

        $result = $query->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            return null;
        }

        return new Word((int) $result['id'], $result['text']);
    }

    public function clearWordTable(): void
    {
        $query = $this->connection->prepare('DELETE FROM words');
        $query->execute();
    }
}
