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

use fpdf\Enums\PdfOrientation;

final class PdfDocPutLinksTest extends AbstractPdfDocTestCase
{
    public function testLandscape(): void
    {
        $doc = $this->createDocument(addPage: false, orientation: PdfOrientation::LANDSCAPE);

        $doc->addPage(PdfOrientation::LANDSCAPE);
        $link = $doc->createLink();
        $doc->cell(text: 'Page Landscape', link: $link);

        $doc->addPage(PdfOrientation::PORTRAIT);
        $link = $doc->createLink();
        $doc->cell(text: 'Page Portrait', link: $link);

        $doc->close();
        self::assertSame(2, $doc->getPage());
    }

    public function testPortait(): void
    {
        $doc = $this->createDocument(addPage: false);

        $doc->addPage(PdfOrientation::PORTRAIT);
        $link = $doc->createLink();
        $doc->cell(text: 'Page Portrait', link: $link);

        $doc->addPage(PdfOrientation::LANDSCAPE);
        $link = $doc->createLink();
        $doc->cell(text: 'Page Landscape', link: $link);

        $doc->close();

        self::assertSame(2, $doc->getPage());
    }
}
