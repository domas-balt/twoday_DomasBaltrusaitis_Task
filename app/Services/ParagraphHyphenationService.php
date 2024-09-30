<?php

declare(strict_types=1);

namespace App\Services;

class ParagraphHyphenationService
{
    private const array DELIMITERS = [',','.','!','?','"',"'",'-','–',"\n",' '];

    private array $hyphenatedLines = [];

    public function __construct(
        private readonly array $paragraphLines,
        private readonly HyphenationServiceInterface $hyphenationService,
    ){
    }

    public function hyphenateParagraph(): array
    {
        foreach ($this->paragraphLines as $key => $paragraphLine) {
            $splitLine = $this->splitLineByDelimiter($paragraphLine);

            foreach ($splitLine as $wordKey => $value) {
                if ($this->isDelimiter($value)) {
                    continue;
                }

                $wordsToHyphenate[] = $value;
                $hyphenatedWord = $this->hyphenationService->hyphenateWords($wordsToHyphenate);
                $splitLine[$wordKey] = $hyphenatedWord[0];
                $wordsToHyphenate = [];
            }

            $this->hyphenatedLines[$key] = implode($splitLine);
        }

        return $this->hyphenatedLines;
    }

    private function splitLineByDelimiter(string $paragraphLine): array
    {
        return preg_split("/([\s,.!?\"'–-]+)/", $paragraphLine, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }

    private function isDelimiter(string $value): bool
    {
        return in_array($value, self::DELIMITERS, true) || is_numeric($value);
    }
}
