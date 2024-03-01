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

#[\PHPUnit\Framework\Attributes\CoversClass(PdfBorder::class)]
class BorderTest extends AbstractTestCase
{
    /**
     * @throws PdfException
     */
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $width = 0.0;
        $height = 5.0;
        $move = PdfMove::BELOW;
        $doc->cell($width, $height, 'None Border', PdfBorder::none(), $move);
        $doc->cell($width, $height, 'All Border', PdfBorder::all(), $move);

        $doc->cell($width, $height, 'Left Border', PdfBorder::left(), $move);
        $doc->cell($width, $height, 'Right Border', PdfBorder::right(), $move);
        $doc->cell($width, $height, 'Top Border', PdfBorder::top(), $move);
        $doc->cell($width, $height, 'Bottom Border', PdfBorder::bottom(), $move);

        $doc->cell($width, $height, 'Left/Right Border', PdfBorder::leftRight(), $move);
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $width = 0.0;
        $height = 5.0;
        $doc->Cell($width, $height, 'None Border', 0, 1);
        $doc->Cell($width, $height, 'All Border', 1, 1);

        $doc->Cell($width, $height, 'Left Border', 'L', 1);
        $doc->Cell($width, $height, 'Right Border', 'R', 1);
        $doc->Cell($width, $height, 'Top Border', 'T', 1);
        $doc->Cell($width, $height, 'Bottom Border', 'B', 1);

        $doc->Cell($width, $height, 'Left/Right Border', 'LR', 1);
    }
}
