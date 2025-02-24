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

use fpdf\PdfDocument;

class PdfCompareLineWidthTest extends AbstractCompareTestCase
{
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->setLineWidth(1);
        $doc->addPage();
        $doc->setLineWidth(2);
    }

    #[\Override]
    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->SetLineWidth(1);
        $doc->AddPage();
        $doc->SetLineWidth(2);
    }
}
