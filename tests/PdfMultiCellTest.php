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

use fpdf\Color\PdfGrayColor;
use fpdf\Color\PdfRgbColor;
use fpdf\Enums\PdfDestination;
use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfLayout;
use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfOrientation;
use fpdf\Enums\PdfRectangleStyle;
use fpdf\Enums\PdfZoom;
use fpdf\PdfBorder;
use fpdf\PdfDocument;
use fpdf\Tests\fixture\PdfMultiCellLines;
use PHPUnit\Framework\TestCase;

class PdfMultiCellTest extends TestCase
{
    private const COMMENT = <<<COMMENT
        This file is part of the 'fpdf' package.

        For the license information, please view the LICENSE
        file that was distributed with this source code.

        @author bibi.nu <bibi@bibi.nu>
        COMMENT;

    public function tesLongTextWithNewLine(): void
    {
        $text = \str_repeat("Fake \n", 250);
        self::assertSameMultiCell($text);
    }

    public function testBasic(): void
    {
        $source = new PdfDocument();
        $this->updateDocument($source);

        $target = new PdfMultiCellLines();
        $this->updateDocument($target);

        $expected = self::cleanContent($source->output(PdfDestination::STRING));
        $actual = self::cleanContent($target->output(PdfDestination::STRING));

        self::assertSame($expected, $actual);
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

    private function updateDocument(PdfDocument $doc): void
    {
        $doc->setCompression(false);
        $doc->addPage();
        $doc->setFont('Arial', PdfFontStyle::BOLD, 16);
        $doc->cell(text: 'This is  test 3456.', move: PdfMove::BELOW);
        $doc->setFont('ZapfDingbats', PdfFontStyle::BOLD, 12);
        $doc->cell(text: 'This is  test 3456.', move: PdfMove::BELOW);
        $doc->multiCell(null, 5.0, "This is multi cells\nNew Line");

        $doc->lineBreak();
        $doc->image(__DIR__ . '/images/image.png');
        $doc->lineBreak(5.0);
        $doc->image(__DIR__ . '/images/image.jpg');
        $doc->lineBreak(5.0);
        $doc->image(__DIR__ . '/images/image.gif');
        $doc->lineBreak(5.0);
        $doc->image(__DIR__ . '/images/image.gif', link: 1);

        $x = $doc->getX();
        $y = $doc->getY();
        $doc->setLineWidth(1.0);
        $doc->line($x, $y, $x + 100.0, $y);

        $doc->link($x, $y, 100, 20, 'https://www.bibi.nu');
        $doc->addLink();
        // //
        $doc->setAuthor('Author Ĝ');
        $doc->setCreator('Creator');
        $doc->setKeywords('Keywords');
        $doc->setSubject('Subject');
        $doc->setTitle('Title');

        $doc->addPage(PdfOrientation::LANDSCAPE);
        $doc->cell(text: 'This is  test 3456.', move: PdfMove::BELOW);

        $doc->setDrawColor(PdfRgbColor::red());
        $x = $doc->getX();
        $y = $doc->getY();
        $doc->setLineWidth(0.5);
        $doc->line($x, $y, $x + 100.0, $y);

        $doc->setFillColor(PdfRgbColor::green());
        $doc->setTextColor(PdfRgbColor::blue());
        $x = $doc->getX();
        $y = $doc->getY() + 10.0;
        $doc->rect($x, $y, 100, 100, PdfRectangleStyle::BOTH);

        $color = PdfGrayColor::instance(255);
        $doc->setDrawColor($color);
        $doc->setFillColor($color);
        $doc->setTextColor($color);
        $doc->setFontSizeInPoint(9.5);

        $doc->text($doc->getX(), $doc->getY(), 'Text');
        $doc->write('Write', 5, 1);

        $doc->setZoom(PdfZoom::FULL_PAGE);
        $doc->setLayout(PdfLayout::SINGLE_PAGE);

        $doc->multiCell(text: self::COMMENT);
    }
}
