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
class OrientationTest extends AbstractTestCase
{
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->cell(0.0, 0.0, 'Portrait');

        $doc->addPage(PdfOrientation::LANDSCAPE);
        $doc->cell(0.0, 0.0, 'Landscape');

        $doc->addPage(PdfOrientation::PORTRAIT, PdfPageSize::A3);
        $doc->cell(0.0, 0.0, 'Portrait');

        $doc->addPage(PdfOrientation::PORTRAIT, PdfSize::instance(100, 100));
        $doc->cell(0.0, 0.0, 'Custom');

        $doc->addPage(PdfOrientation::PORTRAIT, PdfPageSize::A4, PdfRotation::CLOCKWISE_90);
        $doc->cell(0.0, 0.0, 'Portrait 90deg.');

        $doc->addPage(PdfOrientation::PORTRAIT, PdfPageSize::A4, PdfRotation::CLOCKWISE_180);
        $doc->cell(0.0, 0.0, 'Portrait 180deg.');

        $doc->addPage(PdfOrientation::PORTRAIT, PdfPageSize::A4, PdfRotation::CLOCKWISE_270);
        $doc->cell(0.0, 0.0, 'Portrait 270deg.');

        $doc->addPage(PdfOrientation::PORTRAIT, PdfPageSize::A4, PdfRotation::DEFAULT);
        $doc->cell(0.0, 0.0, 'Portrait 0deg.');
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->Cell(0.0, 0.0, 'Portrait');

        $doc->AddPage('L');
        $doc->Cell(0.0, 0.0, 'Landscape');

        $doc->AddPage('P', 'A3');
        $doc->Cell(0.0, 0.0, 'Portrait');

        $doc->AddPage('P', [100, 100]);
        $doc->Cell(0.0, 0.0, 'Custom');

        $doc->AddPage('P', 'A4', 90);
        $doc->Cell(0.0, 0.0, 'Portrait 90deg.');

        $doc->AddPage('P', 'A4', 180);
        $doc->Cell(0.0, 0.0, 'Portrait 180deg.');

        $doc->AddPage('P', 'A4', 270);
        $doc->Cell(0.0, 0.0, 'Portrait 270deg.');

        $doc->AddPage('P', 'A4');
        $doc->Cell(0.0, 0.0, 'Portrait 0deg.');
    }
}
