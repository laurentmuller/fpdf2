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

namespace fpdf;

use PHPUnit\Framework\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(FPDF::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(PdfDocument::class)]
class PdfDocPropertiesTest extends TestCase
{
    public function testAddPageClosed(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $doc->close();
        $doc->addPage();
        self::fail('A PDF exception must be raised.');
    }

    public function testAliasNumberPages(): void
    {
        $doc = $this->createDocument(false, false);
        $doc->setAliasNumberPages();
        $doc->addPage();
        self::assertSame(1, $doc->getPage());
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

    public function testBottomMargin(): void
    {
        $doc = $this->createDocument();
        $expected = $doc->getBottomMargin();
        $doc->setAutoPageBreak(true, $expected);
        self::assertSame($expected, $doc->getBottomMargin());
    }

    public function testCellWithoutFont(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(true, false);
        $doc->cell(text: 'fake');
        self::fail('A PDF exception must be raised.');
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
        $doc->setFillColor(255, 255, 255);
        $doc->setTextColor(0, 0, 0);
        $doc->addPage();
        $doc->setFont(PdfFontName::ARIAL);
        $doc->cell(text: 'fake');
        $doc->text(25, 25, 'fake');
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
        $expected = $doc->getFontSizeInPoint();
        $doc->setFontSizeInPoint($expected);
        self::assertSame($expected, $doc->getFontSizeInPoint());
    }

    public function testFontSizeInPoint(): void
    {
        $doc = $this->createDocument();
        $doc->setFontSizeInPoint(9.0);
        $actual = $doc->getFontSize();
        self::assertSame(3.175, $actual);
    }

    public function testGetStringWidth(): void
    {
        $doc = $this->createDocument(false, false);
        self::assertSame(0.0, $doc->getStringWidth(''));
        $doc->setFont(PdfFontName::ARIAL);
        self::assertSame(0.0, $doc->getStringWidth(''));
        self::assertSame(0.0, $doc->getStringWidth("\n"));
        self::assertEqualsWithDelta(0.88, $doc->getStringWidth(' '), 0.01);
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

    public function testImageEmpty(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $doc->image('');
        self::fail('A PDF exception must be raised.');
    }

    public function testImageEmptyType(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $doc->image(__DIR__);
        self::fail('A PDF exception must be raised.');
    }

    public function testImageWrong(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $doc->image(__FILE__);
        self::fail('A PDF exception must be raised.');
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
        $doc->setLineWidth(2.0);
        self::assertSame(2.0, $doc->getLineWidth());
        $doc->setLineCap(PdfLineCap::ROUND);
        self::assertSame(PdfLineCap::ROUND, $doc->getLineCap());
        $doc->setLineJoin(PdfLineJoin::BEVEL);
        self::assertSame(PdfLineJoin::BEVEL, $doc->getLineJoin());
    }

    public function testLineCountWithoutFont(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $doc->getLinesCount('fake');
        self::fail('A PDF exception must be raised.');
    }

    public function testLineJoin(): void
    {
        $doc = $this->createDocument(false, false);
        $doc->setLineJoin(PdfLineJoin::BEVEL);
        $doc->addPage();
        self::assertSame(1, $doc->getPage());
    }

    public function testLinesCount(): void
    {
        $doc = $this->createDocument();
        self::assertSame(0, $doc->getLinesCount(null));
        self::assertSame(0, $doc->getLinesCount(''));
        self::assertSame(0, $doc->getLinesCount("\n\n"));
        self::assertSame(1, $doc->getLinesCount('fake'));
        self::assertSame(2, $doc->getLinesCount("Firs Line\nSecond Line"));
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

    public function testOutputDownload(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false);
        echo 'fake';
        $doc->output(PdfDestination::DOWNLOAD);
        self::fail('A PDF exception must be raised.');
    }

    public function testOutputDownloadValid(): void
    {
        $doc = $this->createDocument(false);
        $doc->output(PdfDestination::DOWNLOAD);
        self::assertSame(1, $doc->getPage());
    }

    public function testOutputInlineError(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false);
        echo 'fake';
        $doc->output();
        self::fail('A PDF exception must be raised.');
    }

    public function testOutputInlineValid(): void
    {
        $doc = $this->createDocument(false);
        $doc->output();
        self::assertSame(1, $doc->getPage());
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
        $doc->rect(10, 10, 100, 20);
        $doc->rect(10, 20, 100, 20, PdfRectangleStyle::BOTH);
        $doc->rect(10, 30, 100, 20, PdfRectangleStyle::FILL);
        $doc->rect(10, 10, 100, 20, PdfBorder::all());

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

    public function testRightMargin(): void
    {
        $doc = $this->createDocument();
        self::assertEqualsWithDelta(10.0, $doc->getRightMargin(), 0.01);
        $doc->setRightMargin(45.0);
        self::assertEqualsWithDelta(45.0, $doc->getRightMargin(), 0.01);
    }

    public function testSetFontInvalid(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $doc->setFont('Fake');
        self::fail('A PDF exception must be raised.');
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

    public function testTextWithoutFont(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(true, false);
        $doc->text(25, 25, 'fake');
        self::fail('A PDF exception must be raised.');
    }

    public function testTitle(): void
    {
        $doc = $this->createDocument();
        self::assertSame('', $doc->getTitle());
        $doc->setTitle('Title');
        self::assertSame('Title', $doc->getTitle());
    }

    public function testTopMargin(): void
    {
        $doc = $this->createDocument();
        self::assertEqualsWithDelta(10.0, $doc->getTopMargin(), 0.01);
        $doc->setTopMargin(20.0);
        self::assertSame(20.0, $doc->getTopMargin());
    }

    public function testUnderline(): void
    {
        $doc = $this->createDocument();
        $doc->cell(text: 'fake');
        $doc->text(25, 25, '');
        $doc->text(25, 25, 'fake');
        $doc->write(5.0, '');
        $doc->write(5.0, 'fake');
        self::assertSame(1, $doc->getPage());
    }

    public function testUseMargin(): void
    {
        $doc = $this->createDocument();
        $oldMargin = $doc->getCellMargin();
        $doc->useCellMargin(function () use ($doc): void {
            self::assertSame(0.0, $doc->getCellMargin());
        });
        $newMargin = $doc->getCellMargin();
        self::assertSame($oldMargin, $newMargin);
    }

    public function testWriteWithoutFont(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(true, false);
        $doc->write(5.0, 'fake');
        self::fail('A PDF exception must be raised.');
    }

    private function createDocument(bool $addPage = true, bool $addFont = true): PdfDocument
    {
        $doc = new PdfDocument();
        if ($addPage) {
            $doc->addPage();
        }
        if ($addFont) {
            $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR);
        }

        return $doc;
    }
}
