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

final class PdfCompareEscape extends AbstractCompareTestCase
{
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $text = $this->getText();
        $doc->text(5.0, 15.0, $text);
        $doc->write($text);
        $doc->multiCell(text: $text);
    }

    #[\Override]
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
