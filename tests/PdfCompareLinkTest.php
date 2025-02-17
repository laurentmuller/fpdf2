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

class PdfCompareLinkTest extends AbstractCompareTestCase
{
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->link(10, 10, 100, 5, 'https://wwww.bibi.nu');
        $doc->addLink();
    }

    #[\Override]
    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->Link(10, 10, 100, 5, 'https://wwww.bibi.nu');
        $doc->AddLink();
    }
}
