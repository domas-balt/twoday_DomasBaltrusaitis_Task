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
                (words_text)"
            );

            $stmt->execute(['path' => $path]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function clearWordTable(): void
    {
        $stmt = $this->connection->prepare('DELETE FROM words');
        $stmt->execute();
    }
}
