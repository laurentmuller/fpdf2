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

final class PdfDocWordSpacingTest extends AbstractPdfDocTestCase
{
    public function testWordSpacing(): void
    {
        $doc = $this->createDocument();
        $source = ' entry ';
        $width = $doc->getPrintableWidth() / 4.0;
        for ($i = 1; $i < 150; ++$i) {
            $text = \str_repeat($source, $i);
            $doc->multiCell(width: $width, text: $text);
        }
        $page = $doc->getPage();
        self::assertGreaterThan(1, $page);
    }
}
