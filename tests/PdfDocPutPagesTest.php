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

use fpdf\Enums\PdfOrientation;

final class PdfDocPutPagesTest extends AbstractPdfDocTestCase
{
    public function testPutPages(): void
    {
        $doc = $this->createDocument(orientation: PdfOrientation::LANDSCAPE);
        $doc->cell(text: 'Page 1');
        $doc->close();
        self::assertSame(1, $doc->getPage());
    }
}
