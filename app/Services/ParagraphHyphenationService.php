<?php

namespace App\Services;

class ParagraphHyphenationService
{
    private array $splitLine;
    private array $paragraphLineArray;
    private array $syllableArray;
    private array $finalParagraphArray;
    private readonly HyphenationService $hyphenationService;
    private readonly ResultVisualizationService $visualizationService;

    public function __construct(array $paragraphLineArray, array $syllableArray, ResultVisualizationService $visualizationService)
    {
        $this->paragraphLineArray = $paragraphLineArray;
        $this->syllableArray = $syllableArray;
        $this->splitLine = [];
        $this->hyphenationService = new HyphenationService("", $syllableArray);
        $this->visualizationService = $visualizationService;
        $this->finalParagraphArray = [];
    }

    public function hyphenateParagraph(): array
    {
        foreach ($this->paragraphLineArray as $key => $paragraphLine) {
            self::splitLineByDelimiter($paragraphLine);

            for($i = 0; $i < count($this->splitLine); $i++) {
                if ($i % 2 == 0 && !is_numeric($this->splitLine[$i])) {
                    $this->hyphenationService->clearArrays();
                    $this->hyphenationService->setWord(strtolower($this->splitLine[$i]));
                    $this->hyphenationService->hyphenateWord();
                    $hyphenatedWord = $this->hyphenationService->getFinalWord();
                    $processedWord = $this->visualizationService->getProcessedWord($hyphenatedWord);
                    $this->splitLine[$i] = $processedWord;
                }
            }

            $this->finalParagraphArray[$key] = implode($this->splitLine);
        }

        return $this->finalParagraphArray;
    }

    private function splitLineByDelimiter(string $paragraphLine): void
    {
        $this->splitLine = preg_split("/([\s,.!?\"'–-]+)/", $paragraphLine, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }
}