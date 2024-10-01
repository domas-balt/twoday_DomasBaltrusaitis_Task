<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class SyllableRepository
{
    public function __construct(
        private readonly PDO $connection
    ){

    }

    public function loadSyllablesFromFileToDb(string $fileName): void
    {
        $path = dirname(__DIR__) . $fileName;

        if (!file_exists($path)) {
            throw new \InvalidArgumentException("File not found with path {$path} and file name {$fileName}!");
        }

        try {
            $stmt = $this->connection->prepare(
                "LOAD DATA LOCAL INFILE :path INTO TABLE syllables
                FIELDS TERMINATED BY ''
                (syllable_pattern)"
            );

            $stmt->execute(['path' => $path]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getAllSyllables(): array
    {
        $stmt = $this->connection->query('SELECT * FROM syllables');
        $unfilteredSyllableArrays = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $filteredSyllables = [];

        foreach ($unfilteredSyllableArrays as $unfilteredSyllables) {
            $filteredSyllables[] = $unfilteredSyllables['syllable_pattern'];
        }

        return $filteredSyllables;
    }

    public function clearSyllableTable(): void
    {
        $stmt = $this->connection->prepare('DELETE FROM syllables');
        $stmt->execute();
    }
}
