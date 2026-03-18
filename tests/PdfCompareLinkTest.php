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

final class PdfCompareLinkTest extends AbstractCompareTestCase
{
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->link(10, 10, 100, 5, 'https://wwww.bibi.nu');
        $doc->addLink();
    }

    #[\Override]
    protected function updateOldDocument(\FPDF $doc): void
    {
        $doc->Link(10, 10, 100, 5, 'https://wwww.bibi.nu');
        $doc->AddLink();
    }
}
