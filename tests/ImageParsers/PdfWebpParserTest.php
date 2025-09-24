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

namespace fpdf\Tests\ImageParsers;

use fpdf\ImageParsers\PdfWebpParser;

class PdfWebpParserTest extends AbstractPdfParserTestCase
{
    public function testValid(): void
    {
        $file = 'image.webp';
        $image = $this->parseFile($file);
        self::assertSame(320, $image->width);
        self::assertSame(214, $image->height);
    }

    #[\Override]
    protected function createParser(): PdfWebpParser
    {
        return new PdfWebpParser();
    }
}
