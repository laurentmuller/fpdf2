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

use PHPUnit\Framework\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(PdfPngParser::class)]
class PdfPngParserTest extends TestCase
{
    public function testEndOfStream(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new class() extends PdfPngParser {
            protected function parseStream(PdfDocument $parent, $stream, string $file): array
            {
                \fread($stream, (int) \filesize($file));

                return parent::parseStream($parent, $stream, $file);
            }
        };
        $file = __DIR__ . '/images/image.png';
        $parser->parse($parent, $file);
    }

    public function testInvalid(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new PdfPngParser();
        $file = __DIR__ . '/images/image.fake';
        $parser->parse($parent, $file);
    }

    public function testInvalidBpc(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new PdfPngParser();
        $file = __DIR__ . '/images/invalid_bpc.png';
        $parser->parse($parent, $file);
    }

    public function testInvalidColorType(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new PdfPngParser();
        $file = __DIR__ . '/images/invalid_color_type.png';
        $parser->parse($parent, $file);
    }

    public function testInvalidCompression(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new PdfPngParser();
        $file = __DIR__ . '/images/invalid_compression.png';
        $parser->parse($parent, $file);
    }

    public function testInvalidFilter(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new PdfPngParser();
        $file = __DIR__ . '/images/invalid_filter.png';
        $parser->parse($parent, $file);
    }

    public function testInvalidHeaderChunk(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new PdfPngParser();
        $file = __DIR__ . '/images/invalid_header_chunk.png';
        $parser->parse($parent, $file);
    }

    public function testInvalidInterlacing(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new PdfPngParser();
        $file = __DIR__ . '/images/invalid_interlacing.png';
        $parser->parse($parent, $file);
    }

    public function testInvalidSignature(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new PdfPngParser();
        $file = __DIR__ . '/images/invalid_signature.png';
        $parser->parse($parent, $file);
    }

    public function testValid(): void
    {
        $parent = new PdfDocument();
        $parser = new PdfPngParser();
        $file = __DIR__ . '/images/image.png';
        $image = $parser->parse($parent, $file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(124, $image['width']);
        self::assertSame(147, $image['height']);
    }

    public function testValidAlpha(): void
    {
        $parent = new PdfDocument();
        $parser = new PdfPngParser();
        $file = __DIR__ . '/images/alpha_image.png';
        $image = $parser->parse($parent, $file);
        self::assertArrayHasKey('soft_mask', $image);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(31, $image['width']);
        self::assertSame(32, $image['height']);
        self::assertSame('1.4', $parent->getPdfVersion());
    }

    public function testValidGrey(): void
    {
        $parent = new PdfDocument();
        $parser = new PdfPngParser();
        $file = __DIR__ . '/images/grey_image.png';
        $image = $parser->parse($parent, $file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(349, $image['width']);
        self::assertSame(173, $image['height']);
    }
}
