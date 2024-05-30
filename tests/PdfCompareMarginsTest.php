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

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FPDF::class)]
#[CoversClass(PdfDocument::class)]
class PdfCompareMarginsTest extends AbstractCompareTestCase
{
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->setLeftMargin(20);
        $doc->setTopMargin(20);
        $doc->setRightMargin(20);
        $doc->addPage();
        $doc->setMargins(10, 10);
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->SetLeftMargin(20);
        $doc->SetTopMargin(20);
        $doc->SetRightMargin(20);
        $doc->AddPage();
        $doc->SetMargins(10, 10);
    }
}
