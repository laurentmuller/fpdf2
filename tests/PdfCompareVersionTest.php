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

use fpdf\Enums\PdfVersion;
use fpdf\PdfDocument;
use fpdf\Tests\Legacy\FPDF;

final class PdfCompareVersionTest extends AbstractCompareTestCase
{
    public const PdfVersion PDF_VERSION = PdfVersion::VERSION_1_6;

    #[\Override]
    protected function createOldDocument(): FPDF
    {
        $doc = new class extends FPDF {
            #[\Override]
            public function _enddoc(): void
            {
                $this->PDFVersion = PdfCompareVersionTest::PDF_VERSION->value;
                parent::_enddoc();
            }
        };
        $doc->SetCompression(false);
        $doc->SetFont('Arial', '', 9.0);
        $doc->AddPage();

        return $doc;
    }

    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->updatePdfVersion(self::PDF_VERSION);
    }

    #[\Override]
    protected function updateOldDocument(FPDF $doc): void {}
}
