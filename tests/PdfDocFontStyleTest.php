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

use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;

class PdfDocFontStyleTest extends AbstractPdfDocTestCase
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
