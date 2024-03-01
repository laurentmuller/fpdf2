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

#[\PHPUnit\Framework\Attributes\CoversClass(FPDF::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(PdfDocument::class)]
class BasicTest extends AbstractTestCase
{
    private const COMMENT = <<<COMMENT
        This file is part of the 'fpdf' package.

        For the license information, please view the LICENSE
        file that was distributed with this source code.

        @author bibi.nu <bibi@bibi.nu>
        COMMENT;

    public function testAddPageClosed(): void
    {
        self::expectException(PdfException::class);
        $doc = new PdfDocument();
        $doc->close();
        $doc->addPage();
        self::fail('A PDF exception must be raised.');
    }

    public function testAliasNumberPages(): void
    {
        $doc = new PdfDocument();
        $doc->setAliasNumberPages();
        $doc->addPage();
        self::assertSame(1, $doc->getPage());
    }

    public function testCellWithoutFont(): void
    {
        self::expectException(PdfException::class);
        $doc = new PdfDocument();
        $doc->addPage();
        $doc->cell(text: 'fake');
        self::fail('A PDF exception must be raised.');
    }

    public function testColorFlag(): void
    {
        $doc = new PdfDocument();
        $doc->setFillColor(255, 255, 255);
        $doc->setTextColor(0, 0, 0);
        $doc->addPage();
        $doc->setFont(PdfFontName::ARIAL);
        $doc->cell(text: 'fake');
        $doc->text(25, 25, 'fake');
        self::assertSame(1, $doc->getPage());
    }

    public function testFields(): void
    {
        $doc = new PdfDocument();
        $doc->addPage();
        $doc->setFont(PdfFontName::ARIAL);

        $doc->setLineWidth(2.0);
        self::assertSame(2.0, $doc->getLineWidth());
        $doc->setLineCap(PdfLineCap::ROUND);
        self::assertSame(PdfLineCap::ROUND, $doc->getLineCap());
        $doc->setLineJoin(PdfLineJoin::BEVEL);
        self::assertSame(PdfLineJoin::BEVEL, $doc->getLineJoin());

        $actual = $doc->getPosition();
        $doc->setPosition($actual);
        self::assertEqualsCanonicalizing($doc->getPosition(), $actual);
    }

    public function testFontSize(): void
    {
        $doc = new PdfDocument();
        $doc->addPage();
        $doc->setFont(PdfFontName::ARIAL);
        $expected = $doc->getFontSizeInPoint();
        $doc->setFontSizeInPoint($expected);
        self::assertSame($expected, $doc->getFontSizeInPoint());
    }

    public function testLineJoin(): void
    {
        $doc = new PdfDocument();
        $doc->setLineJoin(PdfLineJoin::BEVEL);
        $doc->addPage();
        self::assertSame(1, $doc->getPage());
    }

    public function testOutputDownload(): void
    {
        self::expectException(PdfException::class);
        $doc = new PdfDocument();
        $doc->setFont(PdfFontName::COURIER);
        echo 'fake';
        $doc->output(PdfDestination::DOWNLOAD);
        self::fail('A PDF exception must be raised.');
    }

    public function testOutputDownloadValid(): void
    {
        $doc = new PdfDocument();
        $doc->setFont(PdfFontName::COURIER);
        $doc->output(PdfDestination::DOWNLOAD);
        self::assertSame(1, $doc->getPage());
    }

    public function testOutputInlineError(): void
    {
        self::expectException(PdfException::class);
        $doc = new PdfDocument();
        $doc->setFont(PdfFontName::COURIER);
        echo 'fake';
        $doc->output();
        self::fail('A PDF exception must be raised.');
    }

    public function testOutputInlineValid(): void
    {
        $doc = new PdfDocument();
        $doc->setFont(PdfFontName::COURIER);
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

    public function testRect(): void
    {
        $doc = new PdfDocument();
        $doc->addPage();
        $doc->rect(10, 10, 100, 20);
        $doc->rect(10, 20, 100, 20, PdfRectangleStyle::BOTH);
        $doc->rect(10, 30, 100, 20, PdfRectangleStyle::FILL);
        $doc->rect(10, 10, 100, 20, PdfBorder::all());

        self::assertSame(1, $doc->getPage());
    }

    public function testRectangle(): void
    {
        $doc = new PdfDocument();
        $doc->addPage();
        $rect = PdfRectangle::instance(10, 10, 100, 20);
        $doc->rectangle($rect);
        self::assertSame(1, $doc->getPage());
    }

    public function testSetLink(): void
    {
        $doc = new PdfDocument();
        $doc->addPage();
        $doc->setFont(PdfFontName::ARIAL);
        $doc->setLink(1);
        $doc->setLink(2, -1);
        $doc->setLink(3, 10, 1);
        self::assertSame(1, $doc->getPage());
    }

    public function testTextWithoutFont(): void
    {
        self::expectException(PdfException::class);
        $doc = new PdfDocument();
        $doc->addPage();
        $doc->text(25, 25, 'fake');
        self::fail('A PDF exception must be raised.');
    }

    public function testUnderline(): void
    {
        $doc = new PdfDocument();
        $doc->addPage();
        $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD_UNDERLINE);
        $doc->cell(text: 'fake');
        $doc->text(25, 25, '');
        $doc->text(25, 25, 'fake');
        $doc->write(5.0, '');
        $doc->write(5.0, 'fake');
        self::assertSame(1, $doc->getPage());
    }

    public function testUseMargin(): void
    {
        $doc = new PdfDocument();
        $doc->addPage();
        $doc->setFont(PdfFontName::ARIAL);
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
        $doc = new PdfDocument();
        $doc->addPage();
        $doc->write(5.0, 'fake');
        self::fail('A PDF exception must be raised.');
    }

    /**
     * @throws PdfException
     */
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->setFont('Arial', PdfFontStyle::BOLD, 16);
        $doc->cell(0.0, 5.0, 'This is  test 3456.', move: PdfMove::BELOW);
        $doc->setFont('ZapfDingbats', PdfFontStyle::BOLD, 12);
        $doc->cell(0.0, 5.0, 'This is  test 3456.', move: PdfMove::BELOW);
        $doc->multiCell(0.0, 5.0, "This is multi cells\nNew Line");

        $doc->lineBreak();
        $doc->image(__DIR__ . '/images/image.png');
        $doc->lineBreak(5.0);
        $doc->image(__DIR__ . '/images/image.jpg');
        $doc->lineBreak(5.0);
        $doc->image(__DIR__ . '/images/image.gif');

        $x = $doc->getX();
        $y = $doc->getY();
        $doc->setLineWidth(1.0);
        $doc->line($x, $y, $x + 100.0, $y);

        $doc->link($x, $y, 100, 20, 'https://www.bibi.nu');
        $doc->addLink();

        $doc->setAuthor('Author Ĝ');
        $doc->setCreator('Creator');
        $doc->setKeywords('Keywords');
        $doc->setSubject('Subject');
        $doc->setTitle('Title');

        $doc->addPage(PdfOrientation::LANDSCAPE);
        $doc->cell(0.0, 5.0, 'This is  test 3456.', move: PdfMove::BELOW);

        $doc->setDrawColor(255, 0, 0);
        $x = $doc->getX();
        $y = $doc->getY();
        $doc->setLineWidth(0.5);
        $doc->line($x, $y, $x + 100.0, $y);

        $doc->setFillColor(0, 255, 0);
        $doc->setTextColor(0, 0, 255);
        $x = $doc->getX();
        $y = $doc->getY() + 10.0;
        $doc->rect($x, $y, 100, 100, PdfRectangleStyle::BOTH);

        $doc->setDrawColor(255);
        $doc->setFillColor(255);
        $doc->setTextColor(255);
        $doc->setFontSizeInPoint(9.5);

        $doc->text($doc->getX(), $doc->getY(), 'Text');
        $doc->write(5.0, 'Write', 1);

        $doc->addPage();
        $doc->cell(0.0, 5.0, 'Greek: Γειά σου κόσμος', PdfBorder::all(), PdfMove::RIGHT, PdfTextAlignment::RIGHT, true);

        $doc->setDisplayMode(PdfZoom::FULL_PAGE, PdfLayout::SINGLE);

        $doc->multiCell(0.0, 5.0, self::COMMENT);
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->SetFont('Arial', 'B', 16);
        $doc->Cell(0.0, 5.0, 'This is  test 3456.', ln: 1);
        $doc->SetFont('ZapfDingbats', 'B', 12);
        $doc->Cell(0.0, 5.0, 'This is  test 3456.', ln: 1);
        $doc->MultiCell(0.0, 5.0, "This is multi cells\nNew Line");

        $doc->Ln();
        $doc->Image(__DIR__ . '/images/image.png');
        $doc->Ln(5.0);
        $doc->Image(__DIR__ . '/images/image.jpg');
        $doc->Ln(5.0);
        $doc->Image(__DIR__ . '/images/image.gif');

        /** @phpstan-var float $x */
        $x = $doc->GetX();
        /** @phpstan-var float $y */
        $y = $doc->GetY();
        $doc->SetLineWidth(1.0);
        $doc->Line($x, $y, $x + 100.0, $y);

        $doc->Link($x, $y, 100, 20, 'https://www.bibi.nu');
        $doc->AddLink();

        $doc->SetAuthor('Author Ĝ');
        $doc->SetCreator('Creator');
        $doc->SetKeywords('Keywords');
        $doc->SetSubject('Subject');
        $doc->SetTitle('Title');

        $doc->AddPage('L');
        $doc->Cell(0.0, 5.0, 'This is  test 3456.', ln: 1);

        $doc->SetDrawColor(255, 0, 0);
        /** @phpstan-var float $x */
        $x = $doc->GetX();
        /** @phpstan-var float $y */
        $y = $doc->GetY();
        $doc->SetLineWidth(0.5);
        $doc->Line($x, $y, $x + 100.0, $y);

        $doc->SetFillColor(0, 255, 0);
        $doc->SetTextColor(0, 0, 255);
        /** @phpstan-var float $x */
        $x = $doc->GetX();
        /** @phpstan-var float $y */
        $y = $doc->GetY();
        $y += 10.0;
        $doc->Rect($x, $y, 100, 100, 'FD');

        $doc->SetDrawColor(255);
        $doc->SetFillColor(255);
        $doc->SetTextColor(255);
        $doc->SetFontSize(9.5);

        $doc->Text($doc->GetX(), $doc->GetY(), 'Text');
        $doc->Write(5.0, 'Write', 1);

        $doc->AddPage();
        $doc->Cell(0.0, 5.0, 'Greek: Γειά σου κόσμος', 1, 0, 'R', true);

        $doc->SetDisplayMode('fullpage', 'single');

        $doc->MultiCell(0.0, 5.0, self::COMMENT);
    }
}
