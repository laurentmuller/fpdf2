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

use fpdf\Enums\PdfFontStyle;
use fpdf\PdfDocument;
use fpdf\PdfException;
use fpdf\Tests\Fixture\FPDF;

final class PdfCompareMinimalTest extends AbstractCompareTestCase
{
    /**
     * @throws PdfException
     */
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->getWriter()->setCompression(false);
        $doc->setFont('Arial', PdfFontStyle::BOLD, 16);
    }

    #[\Override]
    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->SetCompression(false);
        $doc->SetFont('Arial', 'B', 16);
    }
}
