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

use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;

final class PdfDocFontStyleTest extends AbstractPdfDocTestCase
{
    public function testFonts(): void
    {
        $doc = $this->createDocument();
        $names = PdfFontName::cases();
        $styles = PdfFontStyle::cases();
        foreach ($names as $name) {
            foreach ($styles as $style) {
                $doc->setFont($name, $style);
            }
        }
        self::assertSame(1, $doc->getPage());
    }
}
