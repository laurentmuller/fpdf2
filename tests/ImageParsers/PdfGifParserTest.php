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

use fpdf\ImageParsers\PdfGifParser;
use fpdf\PdfException;

final class PdfGifParserTest extends AbstractPdfParserTestCase
{
    public function testInvalid(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Missing or incorrect image file:.*image.fake.$/');
        $file = 'image.fake';
        $this->parseFile($file);
    }

    public function testValid(): void
    {
        $file = 'image.gif';
        $image = $this->parseFile($file);
        self::assertSame(400, $image->width);
        self::assertSame(400, $image->height);
    }

    #[\Override]
    protected function createParser(): PdfGifParser
    {
        return new PdfGifParser();
    }
}
