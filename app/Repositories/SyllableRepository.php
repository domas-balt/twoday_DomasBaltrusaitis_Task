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
                (pattern)"
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
            $filteredSyllables[] = $unfilteredSyllables['pattern'];
        }

        return $filteredSyllables;
    }

    public function insertSelectedSyllables(array $selectedSyllables): array
    {
        $selectedSyllableKeys = [];
        $stmt = $this->connection->prepare("INSERT INTO selected_syllables (text) VALUES (:selected_syllable_text)");

        foreach ($selectedSyllables as $selectedSyllable) {
            $stmt->execute(['selected_syllable_text' => $selectedSyllable]);

            $selectedSyllableKeys[] = $this->connection->lastInsertId();
        }

        return $selectedSyllableKeys;
    }

    public function getAllSyllablesByHyphenatedWordId(int $hyphenatedWordId): array
    {
        $stmt = $this->connection->prepare("SELECT * FROM selected_Syllables_hyphenated_Words WHERE hyphenated_word_id = :hyphenated_word_id");
        $stmt->execute(['hyphenated_word_id' => $hyphenatedWordId]);

        $wordSyllableRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $selectedSyllableIds = [];
        $selectedSyllablePatterns = [];

        foreach ($wordSyllableRows as $syllableRow) {
            $selectedSyllableIds[] = $syllableRow['selected_syllable_id'];
        }

        foreach ($selectedSyllableIds as $selectedSyllableId) {
            $stmt = $this->connection->prepare("SELECT text FROM selected_syllables WHERE id = (:selected_syllable_id)");

            $stmt->execute(['selected_syllable_id' => $selectedSyllableId]);

            $selectedPattern = $stmt->fetch(PDO::FETCH_ASSOC);

            $selectedSyllablePatterns[]= $selectedPattern['text'];
        }

        return $selectedSyllablePatterns;
    }

    public function clearSyllableTable(): void
    {
        $stmt = $this->connection->prepare('DELETE FROM syllables');
        $stmt->execute();
    }
}
