<?php

declare(strict_types=1);

namespace App\Server\Services;

use App\Server\Repositories\HyphenatedWordRepository;
use App\Server\Repositories\SelectedSyllableRepository;
use App\Server\Repositories\SyllableRepository;
use JetBrains\PhpStorm\ArrayShape;

readonly class TransactionService
{
    public function __construct(
        private HyphenatedWordRepository $hyphenatedWordRepository,
        private SyllableRepository $syllableRepository,
        private SelectedSyllableRepository $selectedSyllableRepository,
        private \PDO $connection
    ) {
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

            $selectedSyllableIds = $this->selectedSyllableRepository->insertSelectedSyllables($selectedSyllables);

            $this->hyphenatedWordRepository->insertHyphenatedWordAndSyllableIds($selectedSyllableIds, $hyphenatedWordId);

            $this->connection->commit();

            $selectedSyllableEntities = $this->syllableRepository->getAllSyllablesByIds($selectedSyllableIds);

            return [
                'syllables' => $selectedSyllableEntities,
                'hyphenatedWordId' => $hyphenatedWordId
            ];
        } catch (\Exception $e) {
            $this->connection->rollBack();

            throw $e;
        }
    }
}
