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

class PdfCompareEscape extends AbstractCompareTestCase
{
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $text = $this->getText();
        $doc->text(5.0, 15.0, $text);
        $doc->write($text);
        $doc->multiCell(text: $text);
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $text = $this->getText();
        $doc->Text(5.0, 15.0, $text);
        $doc->Write(5.0, $text);
        $doc->MultiCell(0.0, 5.0, $text);
    }

    private function getText(): string
    {
        $text = <<<TEXT
                Parenthesis: (Fake)
                BackSlash: \
            TEXT;

        return $text . "\r";
    }
}
