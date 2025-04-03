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

use fpdf\PdfException;

class PdfDocImageTest extends AbstractPdfDocTestCase
{
    public function testAlpha(): void
    {
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/alpha_image.png');
        $doc->close();
        self::assertSame(1, $doc->getPage());
    }

    public function testEmpty(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Image file name is empty.');
        $doc = $this->createDocument();
        $doc->image('');
    }

    public function testEmptyType(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Image file has no extension and no type was specified:.*/');
        $doc = $this->createDocument();
        $doc->image(__DIR__);
    }

    public function testGrey(): void
    {
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/grey_image.png');
        $doc->close();
        self::assertSame(1, $doc->getPage());
    }

    public function testHeightLessZero(): void
    {
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/image.png', height: -10.0);
        $doc->close();
        self::assertSame(2, $doc->getPage());
    }

    public function testImageCmyk(): void
    {
        $dir = __DIR__ . '/images/';
        $doc = $this->createDocument();
        $doc->image($dir . 'cmyk_image.jpg');

        $doc->close();
        self::assertSame(1, $doc->getPage());
    }

    public function testImages(): void
    {
        $dir = __DIR__ . '/images/';
        $doc = $this->createDocument();
        $doc->image($dir . 'image.jpg');
        $doc->image($dir . 'image.gif');
        $doc->image($dir . 'image.png');
        $doc->image($dir . 'image.webp');
        $doc->image($dir . 'image.bmp');

        $doc->close();
        self::assertSame(3, $doc->getPage());
    }

    public function testInvalid(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Unsupported image type: php.');
        $doc = $this->createDocument();
        $doc->image(__FILE__);
    }

    public function testInvalidBPC(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Bits per component 16 not supported:.*invalid_bpc.png.$/');
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_bpc.png');
    }

    public function testInvalidColorType(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Color type 12 not supported:.*invalid_color_type.png.$/');
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_color_type.png');
    }

    public function testInvalidCompression(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Compression method 16 not supported:.*invalid_compression.png.$/');
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_compression.png');
    }

    public function testInvalidFilter(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Filter method 16 not supported:.*invalid_filter.png.$/');
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_filter.png');
    }

    public function testInvalidGif(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Missing or incorrect image file:.*fake.txt.$/');
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/fake.txt', type: 'gif');
    }

    public function testInvalidHeaderChunk(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Incorrect PNG header chunk \(.*\):.*invalid_header_chunk.png.$/');
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_header_chunk.png');
    }

    public function testInvalidInterlacing(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Interlacing 16 not supported:.*invalid_interlacing.png.$/');
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_interlacing.png');
    }

    public function testInvalidJpeg(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Missing or invalid image size:.*fake.txt.$/');
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/fake.txt', type: 'jpg');
    }

    public function testInvalidPng(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Unable to open image file:.*fake.txt.$/');
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/fake.txt', type: 'png');
    }

    public function testInvalidSignature(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Incorrect PNG header signature:.*invalid_signature.png.$/');
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_signature.png');
    }

    public function testWidthLessZero(): void
    {
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/image.png', width: -10.0);
        $doc->close();
        self::assertSame(2, $doc->getPage());
    }
}
