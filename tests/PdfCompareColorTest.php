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
use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfTextAlignment;
use fpdf\PdfBorder;
use fpdf\PdfDocument;
use fpdf\Tests\Legacy\FPDF;

class PdfCompareColorTest extends AbstractCompareTestCase
{
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->setDrawColor(PdfGrayColor::instance(100));
        $doc->setDrawColor(PdfRgbColor::instance(50, 100, 200));

        $doc->setFillColor(PdfGrayColor::instance(100));
        $doc->setFillColor(PdfRgbColor::instance(50, 100, 200));

        $doc->setTextColor(PdfGrayColor::instance(100));
        $doc->setTextColor(PdfRgbColor::instance(50, 100, 200));

        $doc->cell(null, 5.0, 'Test Color', PdfBorder::all(), PdfMove::BELOW, PdfTextAlignment::RIGHT);
        $doc->cell(null, 5.0, 'Test Color', PdfBorder::all(), PdfMove::BELOW, PdfTextAlignment::CENTER, true);

        $doc->cell(null, 5.0, 'Test Color', PdfBorder::none(), PdfMove::BELOW, PdfTextAlignment::RIGHT);
        $doc->cell(null, 5.0, 'Test Color', PdfBorder::none(), PdfMove::BELOW, PdfTextAlignment::CENTER, true);
    }

    #[\Override]
    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->SetDrawColor(100);
        $doc->SetDrawColor(50, 100, 200);

        $doc->SetFillColor(100);
        $doc->SetFillColor(50, 100, 200);

        $doc->SetTextColor(100);
        $doc->SetTextColor(50, 100, 200);

        $doc->Cell(0.0, 5.0, 'Test Color', 1, 2, 'R');
        $doc->Cell(0.0, 5.0, 'Test Color', 1, 2, 'C', true);

        $doc->Cell(0.0, 5.0, 'Test Color', 0, 2, 'R');
        $doc->Cell(0.0, 5.0, 'Test Color', 0, 2, 'C', true);
    }
}
