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
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfLayout;
use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfOrientation;
use fpdf\Enums\PdfRectangleStyle;
use fpdf\Enums\PdfTextAlignment;
use fpdf\Enums\PdfZoom;
use fpdf\PdfBorder;
use fpdf\PdfDocument;
use fpdf\PdfException;

class PdfCompareBasicTest extends AbstractCompareTestCase
{
    private const COMMENT = <<<COMMENT
        This file is part of the 'fpdf' package.

        For the license information, please view the LICENSE
        file that was distributed with this source code.

        @author bibi.nu <bibi@bibi.nu>
        COMMENT;

    /**
     * @throws PdfException
     */
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
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
        $doc->write('Write', 5.0, 1);

        $doc->addPage();
        $doc->cell(null, 5.0, 'Greek: Γειά σου κόσμος', PdfBorder::all(), PdfMove::RIGHT, PdfTextAlignment::RIGHT, true);

        $doc->setZoom(PdfZoom::FULL_PAGE);
        $doc->setLayout(PdfLayout::SINGLE_PAGE);

        $doc->multiCell(text: self::COMMENT);
    }

    #[\Override]
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
        $doc->Ln(5.0);
        $doc->Image(__DIR__ . '/images/image.gif', link: 1);

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
