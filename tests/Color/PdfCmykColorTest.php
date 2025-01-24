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
        self::assertSame(10, $actual->cyan);
        self::assertSame(20, $actual->magenta);
        self::assertSame(30, $actual->yellow);
        self::assertSame(40, $actual->black);
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
}
