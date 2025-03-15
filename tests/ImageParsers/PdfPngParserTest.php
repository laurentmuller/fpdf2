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

use fpdf\ImageParsers\PdfPngParser;
use fpdf\PdfException;

class PdfPngParserTest extends AbstractPdfParserTestCase
{
    public function testColorType0(): void
    {
        $file = 'image_color_type_0.png';
        $image = $this->parseFile($file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(124, $image['width']);
        self::assertSame(147, $image['height']);
    }

    public function testColorType2(): void
    {
        $file = 'image_color_type_2.png';
        $image = $this->parseFile($file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(124, $image['width']);
        self::assertSame(147, $image['height']);
    }

    public function testEndOfStream(): void
    {
        self::expectException(PdfException::class);
        $file = 'image_truncate.png';
        $this->parseFile($file);
    }

    public function testInvalid(): void
    {
        self::expectException(PdfException::class);
        $file = 'image.fake';
        $this->parseFile($file);
    }

    public function testInvalidBpc(): void
    {
        self::expectException(PdfException::class);
        $file = 'invalid_bpc.png';
        $this->parseFile($file);
    }

    public function testInvalidColorType(): void
    {
        self::expectException(PdfException::class);
        $file = 'invalid_color_type.png';
        $this->parseFile($file);
    }

    public function testInvalidColorTypeWithPalette(): void
    {
        self::expectException(PdfException::class);
        $file = 'invalid_color_type_with_palette.png';
        $this->parseFile($file);
    }

    public function testInvalidCompression(): void
    {
        self::expectException(PdfException::class);
        $file = 'invalid_compression.png';
        $this->parseFile($file);
    }

    public function testInvalidFilter(): void
    {
        self::expectException(PdfException::class);
        $file = 'invalid_filter.png';
        $this->parseFile($file);
    }

    public function testInvalidHeaderChunk(): void
    {
        self::expectException(PdfException::class);
        $file = 'invalid_header_chunk.png';
        $this->parseFile($file);
    }

    public function testInvalidHeaderLength(): void
    {
        self::expectException(PdfException::class);
        $file = 'invalid_header_length.png';
        $this->parseFile($file);
    }

    public function testInvalidInterlacing(): void
    {
        self::expectException(PdfException::class);
        $file = 'invalid_interlacing.png';
        $this->parseFile($file);
    }

    public function testInvalidSignature(): void
    {
        self::expectException(PdfException::class);
        $file = 'invalid_signature.png';
        $this->parseFile($file);
    }

    public function testValid(): void
    {
        $file = 'image.png';
        $image = $this->parseFile($file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(124, $image['width']);
        self::assertSame(147, $image['height']);
    }

    public function testValidAlpha(): void
    {
        $file = 'alpha_image.png';
        $image = $this->parseFile($file);
        self::assertArrayHasKey('soft_mask', $image);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(31, $image['width']);
        self::assertSame(32, $image['height']);
    }

    public function testValidGrey(): void
    {
        $file = 'grey_image.png';
        $image = $this->parseFile($file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(349, $image['width']);
        self::assertSame(173, $image['height']);
    }

    #[\Override]
    protected function createParser(): PdfPngParser
    {
        return new PdfPngParser();
    }
}
