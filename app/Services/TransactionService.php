<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\Syllable;
use App\Repositories\SyllableRepository;
use App\Repositories\WordRepository;
use PDO;

readonly class TransactionService
{
    public function __construct(
        private WordRepository $wordRepository,
        private SyllableRepository $syllableRepository,
        private PDO $connection
    ){
    }

    /**
     * @param Syllable[] $selectedSyllables
     */
    public function syllableWordInsertTransaction(string $word, int $wordPrimaryKey, array $selectedSyllables): array
    {
        try {
            $this->connection->beginTransaction();

            $this->wordRepository->insertHyphenatedWord($word, $wordPrimaryKey);
            $syllableIds = $this->syllableRepository->insertSelectedSyllables($selectedSyllables);

            $this->connection->commit();

            return $syllableIds;

        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new \PDOException($e->getMessage());
        }
    }
}
