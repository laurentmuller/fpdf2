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

use fpdf\ImageParsers\PdfWebpParser;
use fpdf\PdfDocument;
use PHPUnit\Framework\TestCase;

/**
 * @phpstan-import-type ImageType from PdfDocument
 */
class PdfWebpParserTest extends TestCase
{
    public function testValid(): void
    {
        $file = 'image.webp';
        $image = $this->parseFile($file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(320, $image['width']);
        self::assertSame(214, $image['height']);
    }

    /**
     * @phpstan-return ImageType
     */
    private function parseFile(string $file): array
    {
        $parent = new PdfDocument();
        $parser = new PdfWebpParser();

        return $parser->parse($parent, __DIR__ . '/../images/' . $file);
    }
}
