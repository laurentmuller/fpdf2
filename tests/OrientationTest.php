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

class OrientationTest extends AbstractTestCase
{
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->cell(0.0, 0.0, 'Portrait');

        $doc->addPage(PdfOrientation::LANDSCAPE);
        $doc->cell(0.0, 0.0, 'Landscape');

        $doc->addPage(PdfOrientation::PORTRAIT, PdfSize::A5);
        $doc->cell(0.0, 0.0, 'Portrait');

        $doc->addPage(PdfOrientation::PORTRAIT, [100, 100]);
        $doc->cell(0.0, 0.0, 'Custom');

        $doc->addPage(PdfOrientation::PORTRAIT, PdfSize::A4, 90);
        $doc->cell(0.0, 0.0, 'Portrait 90deg.');

        $doc->addPage(PdfOrientation::PORTRAIT, PdfSize::A4, 180);
        $doc->cell(0.0, 0.0, 'Portrait 180deg.');

        $doc->addPage(PdfOrientation::PORTRAIT, PdfSize::A4, 270);
        $doc->cell(0.0, 0.0, 'Portrait 270deg.');
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->Cell(0.0, 0.0, 'Portrait');

        $doc->AddPage('L');
        $doc->Cell(0.0, 0.0, 'Landscape');

        $doc->AddPage('P', 'A5');
        $doc->Cell(0.0, 0.0, 'Portrait');

        $doc->AddPage('P', [100, 100]);
        $doc->Cell(0.0, 0.0, 'Custom');

        $doc->AddPage('P', 'A4', 90);
        $doc->Cell(0.0, 0.0, 'Portrait 90deg.');

        $doc->AddPage('P', 'A4', 180);
        $doc->Cell(0.0, 0.0, 'Portrait 180deg.');

        $doc->AddPage('P', 'A4', 270);
        $doc->Cell(0.0, 0.0, 'Portrait 270deg.');
    }
}
