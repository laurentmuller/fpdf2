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

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PdfDocument::class)]
class PdfDocPutPagesTest extends AbstractPdfDocTestCase
{
    public function testPutPages(): void
    {
        $doc = $this->createDocument(orientation: PdfOrientation::LANDSCAPE);
        $doc->cell(text: 'Page 1');
        $doc->close();
        self::assertSame(1, $doc->getPage());
    }
}
