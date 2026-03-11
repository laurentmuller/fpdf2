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

namespace fpdf\Tests\ImageParsers;

use fpdf\ImageParsers\PdfWebpParser;

final class PdfWebpParserTest extends AbstractPdfParserTestCase
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
