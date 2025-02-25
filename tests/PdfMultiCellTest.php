<?php

/*
 * This file is part of the 'fpdf' package.
 *
 * For the license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author bibi.nu <bibi@bibi.nu>
 */

declare(strict_types=1);

namespace fpdf\Tests;

use fpdf\Enums\PdfDestination;
use fpdf\Enums\PdfFontName;
use fpdf\PdfBorder;
use fpdf\PdfDocument;
use fpdf\Tests\fixture\PdfMultiCellLines;
use PHPUnit\Framework\TestCase;

class PdfMultiCellTest extends TestCase
{
    public function tesLongTextWithNewLine(): void
    {
        $text = \str_repeat("Fake \n", 250);
        self::assertSameMultiCell($text);
    }

    public function testLongText(): void
    {
        $text = \str_repeat('Fake', 250);
        self::assertSameMultiCell($text);
    }

    public function testLongTextWithNewLine(): void
    {
        $text = \str_repeat("Fake\n", 250);
        self::assertSameMultiCell($text);
    }

    public function testLongTextWithSpace(): void
    {
        $text = \str_repeat('Fake ', 250);
        self::assertSameMultiCell($text);
    }

    public function testSingleLine(): void
    {
        $text = 'Fake';
        self::assertSameMultiCell($text);
    }

    public function testSingleLineWithBorder(): void
    {
        $text = 'Fake';
        $border = PdfBorder::all();
        self::assertSameMultiCell($text, $border);
    }

    public function testTwoLines(): void
    {
        $text = "Fake\nFake";
        self::assertSameMultiCell($text);
    }

    public function testTwoLinesWithBorder(): void
    {
        $text = "Fake\nFake";
        $border = PdfBorder::all();
        self::assertSameMultiCell($text, $border);
    }

    public function testTwoLinesWithBorderBottom(): void
    {
        $text = "Fake\nFake";
        $border = PdfBorder::bottom();
        self::assertSameMultiCell($text, $border);
    }

    public function testTwoLinesWithBorderLeftRight(): void
    {
        $text = "Fake\nFake";
        $border = PdfBorder::leftRight();
        self::assertSameMultiCell($text, $border);
    }

    protected static function assertSameMultiCell(string $text, ?PdfBorder $border = null): void
    {
        $border ??= PdfBorder::none();

        $doc1 = new PdfDocument();
        $doc1->setFont(PdfFontName::ARIAL)
            ->addPage();
        $doc1->multiCell(text: $text, border: $border);
        $expected = self::cleanContent($doc1->output(PdfDestination::STRING));

        $doc2 = new PdfMultiCellLines();
        $doc2->setFont(PdfFontName::ARIAL)
            ->addPage();
        $doc2->multiCell(text: $text, border: $border);
        $actual = self::cleanContent($doc2->output(PdfDestination::STRING));

        self::assertSame($expected, $actual);
    }

    private static function cleanContent(string $content): string
    {
        $patterns = [
            '/\/CreationDate.*\)/mi',
            '/\/Producer \(FPDF.*\)/mi',
        ];

        return (string) \preg_replace($patterns, '', $content);
    }
}
