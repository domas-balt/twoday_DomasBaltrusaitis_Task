<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\HyphenatedWord;
use JetBrains\PhpStorm\ArrayShape;

readonly class BasicHyphenationManagementService implements HyphenationManagementInterface
{
    public function __construct(
        private ParagraphHyphenationService $paragraphHyphenationService
    ) {
    }

    #[ArrayShape([
        'hyphenated_word' => 'App\Entities\HyphenatedWord',
        'syllables' => 'App\Entities\SelectedSyllable[]',
    ])] public function manageHyphenation(array $words): array
    {
        $result = [];

        foreach ($words as $key => $word) {
            $result[] = $this->hyphenateWord($word, $key);
        }

        return $result;
    }

    #[ArrayShape([
        'hyphenated_word' => 'App\Entities\HyphenatedWord',
        'syllables' => 'App\Entities\Syllable[]',
    ])]
    private function hyphenateWord(string $word, int $id): array
    {
        $hyphenatedWord = $this->paragraphHyphenationService->hyphenateParagraph($word);

        $hyphenatedWordEntity = new HyphenatedWord($id, $hyphenatedWord, $id);

        $syllables = $this->paragraphHyphenationService->getSyllables();

        return [
            'syllables' => $syllables,
            'hyphenated_word' => $hyphenatedWordEntity,
        ];
    }
}
