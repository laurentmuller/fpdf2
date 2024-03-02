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

namespace fpdf;

#[\PHPUnit\Framework\Attributes\CoversClass(PdfDocument::class)]
class PdfDocImageTest extends AbstractPdfDocTestCase
{
    public function testAlpha(): void
    {
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/alpha_image.png');
        self::assertSame(1, $doc->getPage());
    }

    public function testEmpty(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image('');
        self::fail('A PDF exception must be raised.');
    }

    public function testEmptyType(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__);
        self::fail('A PDF exception must be raised.');
    }

    public function testGrey(): void
    {
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/grey_image.png');
        self::assertSame(1, $doc->getPage());
    }

    public function testHeightLessZero(): void
    {
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/image.png', height: -10.0);
        self::assertSame(2, $doc->getPage());
    }

    public function testInvalid(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__FILE__);
        self::fail('A PDF exception must be raised.');
    }

    public function testInvalidBPC(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_bpc.png');
        self::fail('A PDF exception must be raised.');
    }

    public function testInvalidColorType(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_color_type.png');
        self::fail('A PDF exception must be raised.');
    }

    public function testInvalidCompression(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_compression.png');
        self::fail('A PDF exception must be raised.');
    }

    public function testInvalidFilter(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_filter.png');
        self::fail('A PDF exception must be raised.');
    }

    public function testInvalidGif(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/fake.txt', type: 'gif');
        self::fail('A PDF exception must be raised.');
    }

    public function testInvalidHeaderChunk(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_header_chunk.png');
        self::fail('A PDF exception must be raised.');
    }

    public function testInvalidInterlacing(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_interlacing.png');
        self::fail('A PDF exception must be raised.');
    }

    public function testInvalidJpeg(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/fake.txt', type: 'jpg');
        self::fail('A PDF exception must be raised.');
    }

    public function testInvalidPng(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/fake.txt', type: 'png');
        self::fail('A PDF exception must be raised.');
    }

    public function testInvalidSignature(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/invalid_signature.png');
        self::fail('A PDF exception must be raised.');
    }

    public function testWidthLessZero(): void
    {
        $doc = $this->createDocument();
        $doc->image(__DIR__ . '/images/image.png', width: -10.0);
        self::assertSame(2, $doc->getPage());
    }
}
