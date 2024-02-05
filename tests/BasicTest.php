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

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    private string $newFile = '';
    private string $oldFile = '';

    protected function setUp(): void
    {
        $this->newFile = __DIR__ . '/test_new.pdf';
        $this->oldFile = __DIR__ . '/test_old.pdf';
    }

    #[Depends('testToFileNew')]
    #[Depends('testToFileOld')]
    public function testEqual(): void
    {
        self::assertFileExists($this->oldFile);
        self::assertFileExists($this->newFile);

        $old_content = \file_get_contents($this->oldFile);
        $new_content = \file_get_contents($this->newFile);

        \unlink($this->oldFile);
        \unlink($this->newFile);

        self::assertSame($old_content, $new_content);
    }

    /**
     * @throws PdfException
     */
    public function testToFileNew(): void
    {
        $doc = new PdfDocument();
        $doc->setFont('Arial', PdfFontStyle::BOLD, 16);
        $doc->addPage();
        $doc->cell(0.0, 5.0, 'This is  test 3456.', move: PdfMove::BELOW);
        $doc->setFont('ZapfDingbats', PdfFontStyle::BOLD, 12);
        $doc->cell(0.0, 5.0, 'This is  test 3456.', move: PdfMove::BELOW);
        $doc->multiCell(0.0, 5.0, "This is multi cells\nNew Line");

        $doc->lineFeed();
        $doc->image(__DIR__ . '/android.png');
        $doc->lineFeed(5.0);
        $doc->image(__DIR__ . '/bibi.jpg');

        $x = $doc->getX();
        $y = $doc->getY();
        $doc->setLineWidth(1.0);
        $doc->line($x, $y, $x + 100, $y);

        $doc->link($x, $y, 100, 20, 'https://www.bibi.nu');
        $doc->addLink();

        $doc->setAuthor('Author');
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
        $doc->line($x, $y, $x + 100, $y);

        $doc->setFillColor(0, 255, 0);
        $doc->SetTextColor(0, 0, 255);
        $x = $doc->GetX();
        $y = $doc->GetY() + 10.0;
        $doc->rect($x, $y, 100, 100, PdfRectangleStyle::BOTH);

        $doc->setDrawColor(255);
        $doc->setFillColor(255);
        $doc->setTextColor(255);
        $doc->setFontSize(9.5);

        $doc->text($doc->GetX(), $doc->GetY(), 'Text');
        $doc->write(5.0, 'Write', 1);

        $doc->setDisplayMode(PdfZoom::FULL_PAGE, PdfLayout::SINGLE);
        $doc->output(PdfDestination::FILE, $this->newFile);
        self::assertFileExists($this->newFile);
    }

    public function testToFileOld(): void
    {
        \define('FPDF_FONTPATH', __DIR__ . '/../src/font');

        $doc = new FPDF();
        $doc->SetFont('Arial', 'B', 16);
        $doc->AddPage();
        $doc->Cell(0.0, 5.0, 'This is  test 3456.', ln: 1);
        $doc->SetFont('ZapfDingbats', 'B', 12);
        $doc->Cell(0.0, 5.0, 'This is  test 3456.', ln: 1);
        $doc->MultiCell(0.0, 5.0, "This is multi cells\nNew Line");

        $doc->Ln();
        $doc->Image(__DIR__ . '/android.png');
        $doc->Ln(5.0);
        $doc->Image(__DIR__ . '/bibi.jpg');

        $x = $doc->GetX();
        $y = $doc->GetY();
        $doc->SetLineWidth(1.0);
        $doc->Line($x, $y, $x + 100, $y);

        $doc->Link($x, $y, 100, 20, 'https://www.bibi.nu');
        $doc->AddLink();

        $doc->SetAuthor('Author');
        $doc->SetCreator('Creator');
        $doc->SetKeywords('Keywords');
        $doc->SetSubject('Subject');
        $doc->SetTitle('Title');

        $doc->AddPage('L');
        $doc->Cell(0.0, 5.0, 'This is  test 3456.', ln: 1);

        $doc->SetDrawColor(255, 0, 0);
        $x = $doc->GetX();
        $y = $doc->GetY();
        $doc->SetLineWidth(0.5);
        $doc->Line($x, $y, $x + 100, $y);

        $doc->SetFillColor(0, 255, 0);
        $doc->SetTextColor(0, 0, 255);
        $x = $doc->GetX();
        $y = $doc->GetY() + 10.0;
        $doc->Rect($x, $y, 100, 100, 'FD');

        $doc->SetDrawColor(255);
        $doc->SetFillColor(255);
        $doc->SetTextColor(255);
        $doc->SetFontSize(9.5);

        $doc->Text($doc->GetX(), $doc->GetY(), 'Text');
        $doc->Write(5.0, 'Write', 1);

        $doc->SetDisplayMode('fullpage', 'single');
        $doc->Output('F', $this->oldFile);
        self::assertFileExists($this->oldFile);
    }
}
