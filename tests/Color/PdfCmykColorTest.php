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
use PHPUnit\Framework\TestCase;

class PdfCmykColorTest extends TestCase
{
    public function testBlack(): void
    {
        $actual = PdfCmykColor::black();
        self::assertSameColor($actual, 0, 0, 0, 100);
    }

    public function testCyan(): void
    {
        $actual = PdfCmykColor::cyan();
        self::assertSameColor($actual, 100, 0, 0, 0);
    }

    public function testEquals(): void
    {
        $source = PdfCmykColor::instance(10, 20, 30, 40);
        self::assertTrue($source->equals($source));
        self::assertTrue($source->equals(PdfCmykColor::instance(10, 20, 30, 40)));
        self::assertFalse($source->equals(PdfCmykColor::instance(1, 2, 3, 4)));
        self::assertFalse($source->equals(PdfGrayColor::black()));
        self::assertFalse($source->equals(PdfRgbColor::black()));
    }

    public function testInstance(): void
    {
        $actual = PdfCmykColor::instance(10, 20, 30, 40);
        self::assertSameColor($actual, 10, 20, 30, 40);
    }

    public function testMagenta(): void
    {
        $actual = PdfCmykColor::magenta();
        self::assertSameColor($actual, 0, 100, 0, 0);
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

    public function testToRgbColor(): void
    {
        $color = PdfCmykColor::instance(10, 20, 30, 40);
        $actual = $color->toRgbColor();
        self::assertSame(138, $actual->red);
        self::assertSame(122, $actual->green);
        self::assertSame(107, $actual->blue);
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
        self::assertSameColor($actual, 0, 0, 100, 0);
    }

    protected static function assertSameColor(
        PdfCmykColor $actual,
        int $cyan,
        int $magenta,
        int $yellow,
        int $black
    ): void {
        self::assertSame($cyan, $actual->cyan);
        self::assertSame($magenta, $actual->magenta);
        self::assertSame($yellow, $actual->yellow);
        self::assertSame($black, $actual->black);
    }
}
