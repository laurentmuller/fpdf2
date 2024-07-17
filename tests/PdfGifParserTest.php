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

class PdfGifParserTest extends TestCase
{
    public function testInvalid(): void
    {
        self::expectException(PdfException::class);
        $parent = new PdfDocument();
        $parser = new PdfGifParser();
        $file = __DIR__ . '/images/image.fake';
        $parser->parse($parent, $file);
    }

    public function testValid(): void
    {
        $parent = new PdfDocument();
        $parser = new PdfGifParser();
        $file = __DIR__ . '/images/image.gif';
        $image = $parser->parse($parent, $file);
        self::assertArrayHasKey('width', $image);
        self::assertArrayHasKey('height', $image);
        self::assertSame(400, $image['width']);
        self::assertSame(400, $image['height']);
    }
}
