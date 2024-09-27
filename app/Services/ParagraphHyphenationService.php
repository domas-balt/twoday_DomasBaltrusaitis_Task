<?php
declare(strict_types=1);

namespace App\Services;

class ParagraphHyphenationService
{
    private array $finalParagraphArray = [];

    public function __construct(
        private readonly array $paragraphLineArray,
        private readonly HyphenationServiceInterface $hyphenationService,
    ){
    }

    public function hyphenateParagraph(): array
    {
        foreach ($this->paragraphLineArray as $key => $paragraphLine) {
            $splitLine = $this->splitLineByDelimiter($paragraphLine);
            for($i = 0; $i < count($splitLine); $i++) {
                if ($i % 2 == 0 && !is_numeric($splitLine[$i])) {
                    $wordsToHyphenate[] = $splitLine[$i];
                    $hyphenatedWord = $this->hyphenationService->hyphenateWord($wordsToHyphenate);
                    $splitLine[$i] = $hyphenatedWord[array_rand($hyphenatedWord)];
                }

                $wordsToHyphenate = [];
            }

            $this->finalParagraphArray[$key] = implode($splitLine);
        }

        return $this->finalParagraphArray;
    }

    private function splitLineByDelimiter(string $paragraphLine): array
    {
        return preg_split("/([\s,.!?\"'–-]+)/", $paragraphLine, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }
}