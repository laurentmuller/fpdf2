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

use fpdf\ImageParsers\PdfBmpParser;

class PdfBmpParserTest extends AbstractPdfParserTestCase
{
    public function testValid(): void
    {
        $file = 'image.bmp';
        $image = $this->parseFile($file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(256, $image['width']);
        self::assertSame(256, $image['height']);
    }

    #[\Override]
    protected function createParser(): PdfBmpParser
    {
        return new PdfBmpParser();
    }
}
