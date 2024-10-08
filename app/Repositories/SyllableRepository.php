<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\SelectedSyllable;
use App\Entities\Syllable;
use App\Logger\Logger;
use App\Logger\LogLevel;
use App\Services\FileService;

class SyllableRepository
{
    public function __construct(
        private readonly \PDO $connection,
        private readonly Logger $logger,
    ) {
    }

    public function loadSyllablesFromFile(string $fileName): void
    {
        $path = dirname(__DIR__) . $fileName;

        if (!file_exists($path)) {
            throw new \InvalidArgumentException("File not found with path {$path} and file name {$fileName}!");
        }

        $syllables = FileService::readDataFromFile($fileName);

        try {
            $this->insertManySyllables($syllables);
        } catch (\PDOException $e) {
            $this->logger->log(LogLevel::DEBUG, $e->getMessage());
        }
    }

    public function getAllSyllables(): array
    {
        $query = $this->connection->query('SELECT * FROM syllables');
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

        $query = $this->connection->prepare("INSERT INTO syllables (pattern) VALUES {$placeholders}");
        $query->execute($syllables);
    }

    public function getAllSyllablesByHyphenatedWordId(int $hyphenatedWordId): array
    {
        $query = $this->connection->prepare('SELECT * FROM hyphenated_words_selected_syllables WHERE hyphenated_word_id = :hyphenated_word_id');
        $query->execute(['hyphenated_word_id' => $hyphenatedWordId]);

        $wordSyllableRows = $query->fetchAll(\PDO::FETCH_ASSOC);

        $selectedSyllableIds = [];

        foreach ($wordSyllableRows as $syllableRow) {
            $selectedSyllableIds[] = $syllableRow['selected_syllable_id'];
        }

        return $this->getAllSyllablesByIds($selectedSyllableIds);
    }

    public function clearSyllableTable(): void
    {
        $query = $this->connection->prepare('DELETE FROM syllables');
        $query->execute();
    }

    /**
     * @return SelectedSyllable[]
     */
    public function getAllSyllablesByIds(array $selectedSyllableIds): array
    {
        $placeholders = rtrim(str_repeat('?, ', count($selectedSyllableIds)), ', ');

        $query = $this->connection->prepare("SELECT * FROM selected_syllables WHERE id IN ({$placeholders})");
        $query->execute($selectedSyllableIds);

        $result = $query->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(
            fn (array $syllable): SelectedSyllable => new SelectedSyllable($syllable['id'], $syllable['text']),
            $result,
        );
    }
}
