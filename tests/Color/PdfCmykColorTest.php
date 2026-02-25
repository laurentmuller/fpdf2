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

use fpdf\Color\PdfCmykColor;
use fpdf\Color\PdfGrayColor;
use fpdf\Color\PdfRgbColor;

final class PdfCmykColorTest extends AbstractColorTestCase
{
    public function testBlack(): void
    {
        $actual = PdfCmykColor::black();
        self::assertSameCmykColor($actual, 0, 0, 0, 100);
    }

    public function testCyan(): void
    {
        $actual = PdfCmykColor::cyan();
        self::assertSameCmykColor($actual, 100, 0, 0, 0);
    }

    public function testEquals(): void
    {
        $source = PdfCmykColor::instance(10, 20, 30, 40);
        self::assertEqualsColor($source, $source);
        self::assertEqualsColor($source, PdfCmykColor::instance(10, 20, 30, 40));
        self::assertNotEqualsColor($source, PdfCmykColor::black());
        self::assertNotEqualsColor($source, PdfGrayColor::black());
        self::assertNotEqualsColor($source, PdfRgbColor::black());
    }

    public function testInstance(): void
    {
        $actual = PdfCmykColor::instance(10, 20, 30, 40);
        self::assertSameCmykColor($actual, 10, 20, 30, 40);
    }

    public function testMagenta(): void
    {
        $actual = PdfCmykColor::magenta();
        self::assertSameCmykColor($actual, 0, 100, 0, 0);
    }

    public function testOutput(): void
    {
        $color = PdfCmykColor::instance(0, 0, 0, 0);
        $actual = $color->getOutput();
        self::assertSame('0.000 0.000 0.000 0.000 k', $actual);

        $color = PdfCmykColor::instance(10, 20, 30, 40);
        $actual = $color->getOutput();
        self::assertSame('0.100 0.200 0.300 0.400 k', $actual);
    }

    public function testToCmykColor(): void
    {
        $color = PdfCmykColor::instance(10, 20, 30, 40);
        $actual = $color->toCmykColor();
        self::assertTrue($color === $actual);

    }

    public function testToGrayColor(): void
    {
        $color = PdfCmykColor::instance(0, 0, 0, 0);
        $actual = $color->toGrayColor();
        self::assertSameGrayColor($actual, 255);
    }

    public function testToRgbColor(): void
    {
        $color = PdfCmykColor::instance(10, 20, 30, 40);
        $actual = $color->toRgbColor();
        self::assertSameRgbColor($actual, 138, 122, 107);
    }

    public function testToString(): void
    {
        $color = PdfCmykColor::instance(10, 20, 30, 40);
        $actual = (string) $color;
        self::assertSame('PdfCmykColor(10,20,30,40)', $actual);
    }

    public function testYellow(): void
    {
        $actual = PdfCmykColor::yellow();
        self::assertSameCmykColor($actual, 0, 0, 100, 0);
    }
}
