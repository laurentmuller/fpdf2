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

use fpdf\ImageParsers\PdfJpgParser;
use fpdf\PdfException;

final class PdfJpgParserTest extends AbstractPdfParserTestCase
{
    public function testImageCmyk(): void
    {
        $file = 'cmyk_image.jpg';
        $image = $this->parseFile($file);
        self::assertSame(500, $image->width);
        self::assertSame(333, $image->height);
    }

    public function testImageGray(): void
    {
        $file = 'grey_image.jpg';
        $image = $this->parseFile($file);
        self::assertSame(124, $image->width);
        self::assertSame(147, $image->height);
    }

    public function testInvalid(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Missing or invalid image size:.*image.fake.$/');
        $file = 'image.fake';
        $this->parseFile($file);
    }

    public function testInvalidType(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Invalid JPEG image type \(1\):.*image.gif.$/');
        $file = 'image.gif';
        $this->parseFile($file);
    }

    public function testValid(): void
    {
        $file = 'image.jpg';
        $image = $this->parseFile($file);
        self::assertSame(960, $image->width);
        self::assertSame(684, $image->height);
    }

    #[\Override]
    protected function createParser(): PdfJpgParser
    {
        return new PdfJpgParser();
    }
}
