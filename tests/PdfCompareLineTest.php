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

class PdfCompareLineTest extends AbstractCompareTestCase
{
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->line(10, 10, 100, 100);
        $doc->setLineWidth(1.0);
        $doc->line(10, 20, 100, 100);
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->Line(10, 10, 100, 100);
        $doc->SetLineWidth(1.0);
        $doc->Line(10, 20, 100, 100);
    }
}
