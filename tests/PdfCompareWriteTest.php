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
use fpdf\Tests\Legacy\FPDF;

final class PdfCompareWriteTest extends AbstractCompareTestCase
{
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->write("This is a write test.\nWith multi-lines.");
    }

    #[\Override]
    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->Write(5.0, "This is a write test.\nWith multi-lines.");
    }
}
