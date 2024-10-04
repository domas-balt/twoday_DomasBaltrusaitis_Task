<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\HyphenatedWord;
use App\Entities\SelectedSyllable;
use App\Entities\Syllable;
use App\Repositories\HyphenatedWordRepository;
use App\Repositories\SyllableRepository;
use App\Repositories\WordRepository;
use JetBrains\PhpStorm\ArrayShape;
use PDO;

readonly class TransactionService
{
    public function __construct(
        private HyphenatedWordRepository $hyphenatedWordRepository,
        private SyllableRepository $syllableRepository,
        private PDO $connection
    ){
    }

    #[ArrayShape([
        'hyphenatedWordId' => 'integer',
        'syllables' => 'App\Entities\SelectedSyllable[]',
    ])]
    public function syllableWordInsertTransaction(string $word, int $wordPrimaryKey, array $selectedSyllables): array
    {
        try {
            $this->connection->beginTransaction();

            $hyphenatedWordId = $this->hyphenatedWordRepository->insertHyphenatedWord($word, $wordPrimaryKey);

            $selectedSyllableIds = $this->syllableRepository->insertSelectedSyllables($selectedSyllables);

            $this->hyphenatedWordRepository->insertHyphenatedWordAndSyllableIds($selectedSyllableIds, $hyphenatedWordId);

            $this->connection->commit();

            $selectedSyllableEntities = $this->syllableRepository->getAllSyllablesByIds($selectedSyllableIds);

            return [
                'syllables' => $selectedSyllableEntities,
                'hyphenatedWordId' => $hyphenatedWordId
            ];
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new \PDOException($e->getMessage());
        }
    }
}
