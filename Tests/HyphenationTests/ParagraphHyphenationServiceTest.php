<?php

declare(strict_types=1);

namespace Tests\HyphenationTests;

use App\Services\HyphenationService;
use App\Services\ParagraphHyphenationService;
use PHPUnit\Framework\TestCase;

class ParagraphHyphenationServiceTest extends TestCase
{
    public function testGetSyllables(): void
    {
        $hyphenationServiceMock = $this->createMock(HyphenationService::class);

        $hyphenationServiceMock
            ->method('getSyllables')
            ->willReturn(['.ach4', '.ad4der', '.af1t', '.al3t', '.am5at']);

        $paragraphHyphenationService = new ParagraphHyphenationService($hyphenationServiceMock);

        $syllables = $paragraphHyphenationService->getSyllables();

        $this->assertSame(['.ach4', '.ad4der', '.af1t', '.al3t', '.am5at'], $syllables);
    }

    public function testHyphenateParagraph(): void
    {
        $hyphenationServiceMock = $this->createMock(HyphenationService::class);

        $hyphenationServiceMock
            ->expects($this->exactly(2))
            ->method('hyphenateWords')
            ->willReturn(['mis-trans-late'], ['re-turn']);

        $paragraphHyphenationService = new ParagraphHyphenationService($hyphenationServiceMock);

        $this->assertEquals('mis-trans-late', $paragraphHyphenationService->hyphenateParagraph('mistranslate'));
        $this->assertEquals('re-turn', $paragraphHyphenationService->hyphenateParagraph('return'));
    }
}
