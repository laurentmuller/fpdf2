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

class BorderTest extends AbstractTestCase
{
    /**
     * @throws PdfException
     */
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->cell(0.0, 5.0, 'None Border', false, PdfMove::BELOW);
        $doc->cell(0.0, 5.0, 'All Border', true, PdfMove::BELOW);

        $doc->cell(0.0, 5.0, 'Left Border', 'L', PdfMove::BELOW);
        $doc->cell(0.0, 5.0, 'Right Border', 'R', PdfMove::BELOW);
        $doc->cell(0.0, 5.0, 'Top Border', 'T', PdfMove::BELOW);
        $doc->cell(0.0, 5.0, 'Bottom Border', 'B', PdfMove::BELOW);

        $doc->cell(0.0, 5.0, 'All Border', 'LRTB', PdfMove::BELOW);
        $doc->cell(0.0, 5.0, 'Left/Right Border', 'LR', PdfMove::BELOW);
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->Cell(0.0, 5.0, 'None Border', 0, 1);
        $doc->Cell(0.0, 5.0, 'All Border', 1, 1);

        $doc->Cell(0.0, 5.0, 'Left Border', 'L', 1);
        $doc->Cell(0.0, 5.0, 'Right Border', 'R', 1);
        $doc->Cell(0.0, 5.0, 'Top Border', 'T', 1);
        $doc->Cell(0.0, 5.0, 'Bottom Border', 'B', 1);

        $doc->Cell(0.0, 5.0, 'All Border', 'LRTB', 1);
        $doc->Cell(0.0, 5.0, 'Left/Right Border', 'LR', 1);
    }
}
