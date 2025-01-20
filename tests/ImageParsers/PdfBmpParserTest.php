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
use fpdf\PdfDocument;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type ImageType from PdfDocument
 */
class PdfBmpParserTest extends TestCase
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

    /**
     * @phpstan-return ImageType
     */
    private function parseFile(string $file): array
    {
        $parent = new PdfDocument();
        $parser = new PdfBmpParser();

        return $parser->parse($parent, __DIR__ . '/../images/' . $file);
    }
}
