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
class PdfCompareVersionTest extends AbstractCompareTestCase
{
    public const PDF_VERSION = '1.6';

    protected function createOldDocument(): FPDF
    {
        $doc = new class() extends FPDF {
            public function _enddoc(): void
            {
                $this->PDFVersion = PdfCompareVersionTest::PDF_VERSION;
                parent::_enddoc();
            }
        };
        $doc->SetFont('Arial', '', 9.0);
        $doc->AddPage();

        return $doc;
    }

    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->updatePdfVersion(self::PDF_VERSION);
    }

    protected function updateOldDocument(FPDF $doc): void
    {
    }
}
