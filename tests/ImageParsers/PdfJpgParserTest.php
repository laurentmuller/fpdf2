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
use fpdf\PdfDocument;
use fpdf\PdfException;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type ImageType from PdfDocument
 */
class PdfJpgParserTest extends TestCase
{
    public function testImageCmyk(): void
    {
        $file = 'cmyk_image.jpg';
        $image = $this->parseFile($file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
    }

    public function testImageGray(): void
    {
        $file = 'grey_image.jpg';
        $image = $this->parseFile($file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
    }

    public function testInvalid(): void
    {
        self::expectException(PdfException::class);
        $file = 'image.fake';
        $this->parseFile($file);
    }

    public function testInvalidType(): void
    {
        self::expectException(PdfException::class);
        $file = 'image.gif';
        $this->parseFile($file);
    }

    public function testValid(): void
    {
        $file = 'image.jpg';
        $image = $this->parseFile($file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(960, $image['width']);
        self::assertSame(684, $image['height']);
    }

    /**
     * @phpstan-return ImageType
     */
    private function parseFile(string $file): array
    {
        $parent = new PdfDocument();
        $parser = new PdfJpgParser();

        return $parser->parse($parent, __DIR__ . '/../images/' . $file);
    }
}
