<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Word;
use App\Logger\Logger;
use App\Logger\LogLevel;

class WordRepository
{
    public function __construct(
        private readonly \PDO $connection,
        private readonly Logger $logger
    ) {
    }

    public function uploadWordsFromFile(string $fileName): void
    {
        $path = dirname(__DIR__) . $fileName;

        if (!file_exists($path)) {
            throw new \InvalidArgumentException("File not found with path {$path} and file name {$fileName}!");
        }

        try {
            if (fopen($path, "r") !== false) {
                $query = $this->connection->prepare(
                    "LOAD DATA LOCAL INFILE :path INTO TABLE syllables
                FIELDS TERMINATED BY ''
                (pattern)"
                );

                $query->execute(['path' => $path]);
            }

            $query->execute(['path' => $path]);
        } catch (\PDOException $e) {
            $this->logger->log(LogLevel::DEBUG, $e->getMessage());
        }
    }

    public function getAllWords(): array
    {
        $words = [];

        $query = $this->connection->prepare("SELECT words.text, words.id FROM words LEFT JOIN hyphenationdb.hyphenated_words hw on words.id = hw.word_id WHERE hw.word_id IS NULL");
        $query->execute();

        $wordRows = $query->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($wordRows as $wordRow) {
            $words[$wordRow['id']] = $wordRow['text'];
        }

        return $words;
    }

    public function insertWord(string $word): Word
    {
        $query = $this->connection->prepare("INSERT INTO words (text) VALUES (:words_text)");
        $query->execute(['words_text' => $word]);

        $id = $this->connection->lastInsertId();

        return new Word((int) $id, $word);
    }

    public function checkIfWordExistsDb(string $word): bool
    {
        $query = $this->connection->prepare("SELECT * FROM words WHERE text = :text");
        $query->execute(['text' => $word]);

        $result = $query->fetch(\PDO::FETCH_ASSOC);

        return !empty($result);
    }

    public function findWordByText(string $text): ?Word
    {
        $query = $this->connection->prepare("SELECT * FROM words WHERE text = :text");
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
