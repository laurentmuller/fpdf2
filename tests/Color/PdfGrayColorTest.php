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

namespace fpdf\Tests\Color;

use fpdf\Color\PdfGrayColor;
use fpdf\Color\PdfRgbColor;
use PHPUnit\Framework\TestCase;

class PdfGrayColorTest extends TestCase
{
    public function testBlack(): void
    {
        $color = PdfGrayColor::black();
        $actual = $color->level;
        self::assertSame(0, $actual);
    }

    public function testEquals(): void
    {
        $source = PdfGrayColor::instance(50);
        self::assertTrue($source->equals($source));
        self::assertTrue($source->equals(PdfGrayColor::instance(50)));
        self::assertFalse($source->equals(PdfGrayColor::black()));
        self::assertFalse($source->equals(PdfRgbColor::black()));
    }

    public function testInstance(): void
    {
        $expected = 125;
        $color = PdfGrayColor::instance($expected);
        $actual = $color->level;
        self::assertSame($expected, $actual);
    }

    public function testOutput(): void
    {
        $color = PdfGrayColor::white();
        $actual = $color->getOutput();
        self::assertSame('1.000 g', $actual);

        $color = PdfGrayColor::instance(100);
        $actual = $color->getOutput();
        self::assertSame('0.392 g', $actual);
    }

    public function testToString(): void
    {
        $color = PdfGrayColor::white();
        $actual = (string) $color;
        self::assertSame('PdfGrayColor(255)', $actual);
    }

    public function testWhite(): void
    {
        $color = PdfGrayColor::white();
        $actual = $color->level;
        self::assertSame(255, $actual);
    }
}
