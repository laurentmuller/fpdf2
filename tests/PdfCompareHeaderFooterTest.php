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

use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfTextAlignment;
use fpdf\PdfBorder;
use fpdf\PdfDocument;
use fpdf\Tests\Legacy\FPDF;

class PdfCompareHeaderFooterTest extends AbstractCompareTestCase
{
    #[\Override]
    protected function createNewDocument(): PdfDocument
    {
        $doc = new class() extends PdfDocument {
            #[\Override]
            public function header(): void
            {
                $this->setFont('Arial', PdfFontStyle::BOLD, 15);
                $this->cell(80);
                $this->cell(
                    30,
                    10,
                    'Title',
                    PdfBorder::all(),
                    PdfMove::RIGHT,
                    PdfTextAlignment::CENTER
                );
                $this->lineBreak(20);
            }

            #[\Override]
            public function footer(): void
            {
                $this->setY(-15);
                $this->setFont('Arial', PdfFontStyle::ITALIC, 8);
                $this->cell(
                    null,
                    10,
                    \sprintf('Page %d', $this->getPage()),
                    PdfBorder::none(),
                    PdfMove::RIGHT,
                    PdfTextAlignment::CENTER
                );
            }
        };
        $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR, 9.0);
        $doc->addPage();

        return $doc;
    }

    #[\Override]
    protected function createOldDocument(): FPDF
    {
        $doc = new class() extends FPDF {
            #[\Override]
            public function Header(): void
            {
                $this->SetFont('Arial', 'B', 15);
                $this->Cell(80);
                $this->Cell(30, 10, 'Title', 1, 0, 'C');
                $this->Ln(20);
            }

            #[\Override]
            public function Footer(): void
            {
                /** @phpstan-var int $page */
                $page = $this->PageNo();
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 10, \sprintf('Page %d', $page), 0, 0, 'C');
            }
        };
        $doc->SetFont('Arial', '', 9.0);
        $doc->AddPage();

        return $doc;
    }

    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->cell(height: 5.0, text: 'This is a header/footer test.');
        $doc->addPage();
        $doc->cell(height: 5.0, text: 'This is a header/footer test.');
    }

    #[\Override]
    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->Cell(w: 0.0, h: 5.0, txt: 'This is a header/footer test.');
        $doc->AddPage();
        $doc->Cell(w: 0.0, h: 5.0, txt: 'This is a header/footer test.');
    }
}
