<?php

/*
 * This file is part of the FPDF2 package.
 *
 * (c) bibi.nu <bibi@bibi.nu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace fpdf\Tests;

use fpdf\PdfDocument;
use fpdf\Tests\Fixture\FPDF;

final class PdfCompareMarginsTest extends AbstractCompareTestCase
{
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->setLeftMargin(20);
        $doc->setTopMargin(20);
        $doc->setRightMargin(20);
        $doc->addPage();
        $doc->setMargins(10, 10);
    }

    #[\Override]
    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->SetLeftMargin(20);
        $doc->SetTopMargin(20);
        $doc->SetRightMargin(20);
        $doc->AddPage();
        $doc->SetMargins(10, 10);
    }
}
