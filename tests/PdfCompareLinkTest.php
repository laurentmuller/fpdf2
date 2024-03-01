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
class PdfCompareLinkTest extends AbstractCompareTestCase
{
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->link(10, 10, 100, 5, 'https://wwww.bibi.nu');
        $doc->addLink();
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->Link(10, 10, 100, 5, 'https://wwww.bibi.nu');
        $doc->AddLink();
    }
}
