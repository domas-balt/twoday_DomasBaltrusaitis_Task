<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\HyphenatedWord;
use App\Repositories\HyphenatedWordRepository;
use App\Repositories\SyllableRepository;
use App\Repositories\WordRepository;
use JetBrains\PhpStorm\ArrayShape;

readonly class DatabaseHyphenationManagementService implements HyphenationManagementInterface
{
    public function __construct(
        private TransactionService $transactionService,
        private ParagraphHyphenationService $paragraphHyphenationService,
        private WordRepository $wordRepository,
        private SyllableRepository $syllableRepository,
        private HyphenatedWordRepository $hyphenatedWordRepository
    ) {
    }

    public function manageHyphenation(array $words): array
    {
        $result = [];

        foreach ($words as $word) {
            $result[] = $this->hyphenateAndSaveWord($word);
        }

        return $result;
    }

    #[ArrayShape([
        'hyphenated_word' => 'App\Entities\HyphenatedWord',
        'syllables' => 'App\Entities\SelectedSyllable[]',
    ])]
    private function hyphenateAndSaveWord(string $word): array
    {
        $wordEntity = $this->wordRepository->findWordByText($word);

        if ($wordEntity !== null) {
            $hyphenatedWord = $this->hyphenatedWordRepository->findHyphenatedWordById($wordEntity->getId());
        } else {
            $wordEntity = $this->wordRepository->insertWord($word);
            $hyphenatedWord = null;
        }

        $this->syllableRepository->getAllSyllablesByHyphenatedWordId(57901);

        if ($hyphenatedWord !== null) {
            return [
                'syllables' => $this->syllableRepository->getAllSyllablesByHyphenatedWordId($hyphenatedWord->getId()),
                'hyphenated_word' => $hyphenatedWord,
            ];
        }

        $hyphenatedWord = $this->paragraphHyphenationService->hyphenateParagraph($wordEntity->getText());

        $syllables = $this->paragraphHyphenationService->getSyllables();

        $data = $this->transactionService->syllableWordInsertTransaction($hyphenatedWord, $wordEntity->getId(), $syllables);

        return [
            'syllables' => $data['syllables'],
            'hyphenated_word' => new HyphenatedWord($data['hyphenatedWordId'], $hyphenatedWord, $wordEntity->getId()),
        ];
    }
}
