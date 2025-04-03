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

use fpdf\Color\PdfRgbColor;
use fpdf\Enums\PdfDestination;
use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfLayout;
use fpdf\Enums\PdfLineCap;
use fpdf\Enums\PdfLineJoin;
use fpdf\Enums\PdfPageMode;
use fpdf\Enums\PdfRectangleStyle;
use fpdf\Enums\PdfState;
use fpdf\Enums\PdfUnit;
use fpdf\Enums\PdfVersion;
use fpdf\Enums\PdfZoom;
use fpdf\PdfBorder;
use fpdf\PdfDocument;
use fpdf\PdfException;
use fpdf\PdfPoint;
use fpdf\PdfRectangle;
use fpdf\PdfSize;
use PHPUnit\Framework\Attributes\DataProvider;

class PdfDocPropertiesTest extends AbstractPdfDocTestCase
{
    public static function getIsLinks(): \Generator
    {
        yield [null, false];
        yield ['', false];
        yield [0, false];
        yield [-1, false];

        yield ['link', true];
        yield [1, true];
    }

    public function testAddPageClosed(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('The document is closed.');
        $doc = $this->createDocument(false, false);
        $doc->close();
        $doc->addPage();
    }

    public function testAliasNumberPages(): void
    {
        $doc = $this->createDocument(false, false);
        $actual = $doc->getAliasNumberPages();
        self::assertSame('{nb}', $actual);

        $doc->setAliasNumberPages();
        $actual = $doc->getAliasNumberPages();
        self::assertSame('{nb}', $actual);

        $doc->setAliasNumberPages('{pages}');
        $actual = $doc->getAliasNumberPages();
        self::assertSame('{pages}', $actual);
    }

    public function testAutoPageBreak(): void
    {
        $doc = $this->createDocument();
        self::assertTrue($doc->isAutoPageBreak());
        $doc->setAutoPageBreak(false);
        self::assertFalse($doc->isAutoPageBreak());
        $doc->setAutoPageBreak(true);
        self::assertTrue($doc->isAutoPageBreak());
    }

    public function testCellWithoutFont(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('No font is set.');
        $doc = $this->createDocument(true, false);
        $doc->cell(text: 'fake');
    }

    public function testClose2Times(): void
    {
        $doc = $this->createDocument(true, false);
        $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD_UNDERLINE);
        $doc->close();
        self::assertSame(PdfState::CLOSED, $doc->getState());
        $doc->close();
        self::assertSame(PdfState::CLOSED, $doc->getState());
    }

    public function testColorFlag(): void
    {
        $doc = $this->createDocument(false, false);
        $doc->setFillColor(PdfRgbColor::white());
        $doc->setTextColor(PdfRgbColor::black());
        $doc->addPage();
        $doc->setFont(PdfFontName::ARIAL);
        $doc->cell(text: 'fake');
        self::assertSame(1, $doc->getPage());
    }

    public function testCompression(): void
    {
        $doc = $this->createDocument();
        $doc->setCompression(false);
        self::assertSame(1, $doc->getPage());
    }

    public function testCreateLink(): void
    {
        $doc = $this->createDocument();
        $doc->createLink();
        self::assertSame(1, $doc->getPage());
    }

    public function testFontSize(): void
    {
        $doc = $this->createDocument();
        // default
        $scale = PdfUnit::MILLIMETER->getScaleFactor();
        $expected = 9.0 / $scale;
        self::assertSame($expected, $doc->getFontSize());

        $expected = 12.0 / $scale;
        $doc->setFontSize($expected);
        self::assertSame($expected, $doc->getFontSize());
    }

    public function testFontSizeInPoint(): void
    {
        $doc = $this->createDocument();
        // default
        self::assertSame(9.0, $doc->getFontSizeInPoint());
        // just for code coverage
        $doc->setFontSizeInPoint(9.0);

        $expected = 10.5;
        $doc->setFontSizeInPoint($expected);
        self::assertSame($expected, $doc->getFontSizeInPoint());
    }

    public function testGetStringWidth(): void
    {
        $doc = $this->createDocument(false, false);
        self::assertSame(0.0, $doc->getStringWidth(''));
        $doc->setFont(PdfFontName::ARIAL);
        self::assertSame(0.0, $doc->getStringWidth(''));
        self::assertSame(0.0, $doc->getStringWidth("\r"));
        self::assertSame(0.0, $doc->getStringWidth("\r\r"));
        self::assertEqualsWithDelta(0.88, $doc->getStringWidth("\n"), 0.01);
        self::assertEqualsWithDelta(0.88, $doc->getStringWidth(' '), 0.01);
        self::assertEqualsWithDelta(0.88, $doc->getStringWidth("\r\n\r"), 0.01);
    }

    public function testHorizontalLine(): void
    {
        $doc = $this->createDocument();
        $y = $doc->getY();
        $doc->horizontalLine();
        self::assertSame($y + 2.0, $doc->getY());
        $y = $doc->getY();
        $doc->horizontalLine(5.0, 5.0);
        self::assertSame($y + 10.0, $doc->getY());
        $y = $doc->getY();
        $doc->horizontalLine(1.0, 2.0, 3.0);
        self::assertSame($y + 3.0, $doc->getY());
    }

    public function testHttpEncode(): void
    {
        $this->expectOutputRegex('/CreationDate/');
        $doc = $this->createDocument();
        $doc->output(PdfDestination::DOWNLOAD, '¢FAKE¢');
    }

    #[DataProvider('getIsLinks')]
    public function testIsLink(string|int|null $link, bool $expected): void
    {
        $actual = PdfDocument::isLink($link);
        self::assertSame($expected, $actual);
    }

    public function testLastHeight(): void
    {
        $doc = $this->createDocument();
        $doc->cell(text: '(fake');
        self::assertSame(5.0, $doc->getLastHeight());
        $doc->cell(height: 10.0, text: 'fake');
        self::assertSame(10.0, $doc->getLastHeight());
        self::assertEqualsWithDelta(10.0, $doc->getLeftMargin(), 0.01);
    }

    public function testLayout(): void
    {
        $doc = $this->createDocument();
        self::assertSame(PdfLayout::getDefault(), $doc->getLayout());
        $doc->setLayout(PdfLayout::ONE_COLUMN);
        self::assertSame(PdfLayout::ONE_COLUMN, $doc->getLayout());
    }

    public function testLine(): void
    {
        $doc = $this->createDocument();
        // just to covert code
        $doc->line(0, 0, 0, 0);
        $doc->linePoints(new PdfPoint(0, 0), new PdfPoint(0, 0));
        self::assertSame(1, $doc->getPage());
    }

    public function testLineCap(): void
    {
        $doc = $this->createDocument();
        self::assertSame(PdfLineCap::getDefault(), $doc->getLineCap());
        $doc->setLineCap(PdfLineCap::ROUND);
        self::assertSame(PdfLineCap::ROUND, $doc->getLineCap());
    }

    public function testLineCountWithoutFont(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('No font is set.');
        $doc = $this->createDocument(false, false);
        $doc->getLinesCount('fake');
    }

    public function testLineJoin(): void
    {
        $doc = $this->createDocument(false, false);
        self::assertSame(PdfLineJoin::getDefault(), $doc->getLineJoin());
        $doc->setLineJoin(PdfLineJoin::BEVEL);
        self::assertSame(PdfLineJoin::BEVEL, $doc->getLineJoin());
        $doc->addPage();
        self::assertSame(1, $doc->getPage());
    }

    public function testLinesCount(): void
    {
        $doc = $this->createDocument();
        self::assertSame(0, $doc->getLinesCount(null));
        self::assertSame(0, $doc->getLinesCount(''));
        self::assertSame(0, $doc->getLinesCount("\r"));
        self::assertSame(2, $doc->getLinesCount("\r\n"));
        self::assertSame(3, $doc->getLinesCount("\n\n"));
        self::assertSame(1, $doc->getLinesCount('fake'));
        self::assertSame(2, $doc->getLinesCount("First Line\nSecond Line"));
    }

    public function testLinesCountNoSepIndex(): void
    {
        $doc = $this->createDocument();
        $text = \str_repeat('w', 20);
        $lines = $doc->getLinesCount($text, 2.0, 0.0);
        self::assertSame(21, $lines);
    }

    public function testLineWidth(): void
    {
        $doc = $this->createDocument();
        $doc->setLineWidth(2.0);
        self::assertSame(2.0, $doc->getLineWidth());
    }

    public function testMoveY(): void
    {
        $doc = $this->createDocument();

        $y = $doc->getY();
        $x = $doc->getX();
        $doc->moveY(10.0, false);
        self::assertSame($x, $doc->getX());
        self::assertSame($y + 10.0, $doc->getY());

        $y = $doc->getY();
        $doc->moveY(10.0);
        self::assertSame($x, $doc->getLeftMargin());
        self::assertSame($y + 10.0, $doc->getY());
    }

    public function testPageMode(): void
    {
        $doc = $this->createDocument();
        self::assertSame(PdfPageMode::getDefault(), $doc->getPageMode());
        $doc->setPageMode(PdfPageMode::FULL_SCREEN);
        self::assertSame(PdfPageMode::FULL_SCREEN, $doc->getPageMode());
    }

    public function testPixels2mm(): void
    {
        $doc = new PdfDocument(unit: PdfUnit::INCH);
        $actual = $doc->pixels2mm(10.0, 0.0);
        self::assertSame(10.0, $actual);
        $actual = $doc->pixels2mm(1.0);
        self::assertEqualsWithDelta(0.35, $actual, 0.1);
    }

    public function testPixels2UserUnit(): void
    {
        $doc = new PdfDocument(unit: PdfUnit::POINT);
        $actual = $doc->pixels2UserUnit(1.0);
        self::assertEqualsWithDelta(0.75, $actual, 0.1);
    }

    public function testPoints2UserUnit(): void
    {
        $doc = new PdfDocument(unit: PdfUnit::POINT);
        $actual = $doc->points2UserUnit(10.0);
        self::assertEqualsWithDelta(10.0, $actual, 0.1);
    }

    public function testPosition(): void
    {
        $doc = $this->createDocument();
        $expected = $doc->getPosition();
        $doc->setPosition($expected);
        self::assertEqualsCanonicalizing($expected, $doc->getPosition());
    }

    public function testPrintableWidth(): void
    {
        $doc = $this->createDocument();
        self::assertEqualsWithDelta(190.0, $doc->getPrintableWidth(), 0.01);
    }

    public function testRect(): void
    {
        $doc = $this->createDocument();
        $doc->rect(10, 10, 100, 10);
        $doc->rect(10, 20, 100, 10, PdfRectangleStyle::BOTH);
        $doc->rect(10, 30, 100, 10, PdfRectangleStyle::FILL);
        $doc->rect(10, 40, 100, 10, PdfBorder::all());
        $doc->rect(10, 50, 100, 10, PdfBorder::all(), 'https://www.example.com/');
        $doc->rect(10, 60, 100, 10, PdfRectangleStyle::BOTH, 'https://www.example.com/');

        self::assertSame(1, $doc->getPage());
    }

    public function testRectangle(): void
    {
        $doc = $this->createDocument();
        $rect = PdfRectangle::instance(10, 10, 100, 20);
        $doc->rectangle($rect);
        self::assertSame(1, $doc->getPage());
    }

    public function testRemainingWidth(): void
    {
        $doc = $this->createDocument();
        self::assertEqualsWithDelta(190.0, $doc->getRemainingWidth(), 0.01);
    }

    public function testSetFontInvalid(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Undefined font: fake.');
        $doc = $this->createDocument(false, false);
        $doc->setFont('Fake');
    }

    public function testSetLink(): void
    {
        $doc = $this->createDocument();
        $doc->setLink(1);
        $doc->setLink(2, -1);
        $doc->setLink(3, 10, 1);
        self::assertSame(1, $doc->getPage());
    }

    public function testSetX(): void
    {
        $doc = $this->createDocument();

        $x = $doc->getX();
        $doc->setX($x + 10.0);
        self::assertSame($x + 10.0, $doc->getX());

        $doc->setX(-10.0);
        $expected = $doc->getPageWidth() - 10.0;
        self::assertSame($expected, $doc->getX());
    }

    public function testSetXY(): void
    {
        $doc = $this->createDocument();

        $x = 45.5;
        $y = 75.3;
        $doc->setXY($x, $y);
        self::assertSame($x, $doc->getX());
        self::assertSame($y, $doc->getY());

        $position = $doc->getPosition();
        self::assertSame($x, $position->x);
        self::assertSame($y, $position->y);
    }

    public function testSetY(): void
    {
        $doc = $this->createDocument();

        $y = $doc->getY();
        $doc->setY($y + 50.0);
        self::assertSame($y + 50.0, $doc->getY());

        $doc->setY(-10.0);
        $expected = $doc->getPageHeight() - 10.0;
        self::assertSame($expected, $doc->getY());
    }

    public function testSwapPageSize(): void
    {
        $size = new PdfSize(200, 100);
        $doc = new PdfDocument(size: $size);
        self::assertSame($size->height, $doc->getPageWidth());
        self::assertSame($size->width, $doc->getPageHeight());
    }

    public function testTitle(): void
    {
        $doc = $this->createDocument();
        self::assertSame('', $doc->getTitle());
        $doc->setTitle('Title');
        self::assertSame('Title', $doc->getTitle());
    }

    public function testUnderline(): void
    {
        $doc = $this->createDocument();
        $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::UNDERLINE);
        $doc->cell(text: 'fake');
        self::assertSame(1, $doc->getPage());
    }

    public function testVersion(): void
    {
        $doc = $this->createDocument();
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
        $doc->updatePdfVersion(PdfVersion::VERSION_1_3);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
        $doc->updatePdfVersion(PdfVersion::VERSION_1_4);
        self::assertSame(PdfVersion::VERSION_1_4, $doc->getPdfVersion());
    }

    public function testZoom(): void
    {
        $doc = $this->createDocument();
        self::assertSame(PdfZoom::getDefault(), $doc->getZoom());
        $doc->setZoom(PdfZoom::FULL_PAGE);
        self::assertSame(PdfZoom::FULL_PAGE, $doc->getZoom());
    }
}
