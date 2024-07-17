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

class PdfJpgParserTest extends TestCase
{
    public function testInvalid(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new PdfJpgParser();
        $file = __DIR__ . '/images/image.fake';
        $parser->parse($parent, $file);
    }

    public function testInvalidType(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new PdfJpgParser();
        $file = __DIR__ . '/images/image.gif';
        $parser->parse($parent, $file);
    }

    public function testValid(): void
    {
        $parent = new PdfDocument();
        $parser = new PdfJpgParser();
        $file = __DIR__ . '/images/image.jpg';
        $image = $parser->parse($parent, $file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(960, $image['width']);
        self::assertSame(684, $image['height']);
    }
}
