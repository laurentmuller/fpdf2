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
class LineWidthTest extends AbstractTestCase
{
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->setLineWidth(1);
        $doc->addPage();
        $doc->setLineWidth(2);
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->SetLineWidth(1);
        $doc->AddPage();
        $doc->SetLineWidth(2);
    }
}
