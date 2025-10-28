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
use fpdf\Tests\Legacy\FPDF;

final class PdfCompareImageTest extends AbstractCompareTestCase
{
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->image(__DIR__ . '/images/image.png');
        $doc->lineBreak(5.0);
        $doc->image(__DIR__ . '/images/image.jpg');
        $doc->lineBreak(5.0);
        $doc->image(__DIR__ . '/images/image.gif');
    }

    #[\Override]
    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->Image(__DIR__ . '/images/image.png');
        $doc->Ln(5.0);
        $doc->Image(__DIR__ . '/images/image.jpg');
        $doc->Ln(5.0);
        $doc->Image(__DIR__ . '/images/image.gif');
    }
}
