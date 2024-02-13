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

class TextTest extends AbstractTestCase
{
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->text(5.0, 15.0, "This is a text test.\nWith multi-lines.");
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->Text(5.0, 15.0, "This is a text test.\nWith multi-lines.");
    }
}
