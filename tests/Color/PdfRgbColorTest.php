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

class PdfRgbColorTest extends TestCase
{
    public function testBlack(): void
    {
        $actual = PdfRgbColor::black();
        self::assertSameColor($actual, 0, 0, 0);
    }

    public function testBlue(): void
    {
        $actual = PdfRgbColor::blue();
        self::assertSameColor($actual, 0, 0, 255);
    }

    public function testCmykColor(): void
    {
        $color = PdfRgbColor::instance(10, 20, 30);
        $actual = $color->toCmykColor();
        self::assertSame(67, $actual->cyan);
        self::assertSame(33, $actual->magenta);
        self::assertSame(0, $actual->yellow);
        self::assertSame(88, $actual->black);
    }

    public function testCreate(): void
    {
        $actual = PdfRgbColor::create(null);
        self::assertNull($actual);

        $actual = PdfRgbColor::create('');
        self::assertNull($actual);

        $actual = PdfRgbColor::create('FA');
        self::assertNull($actual);

        $actual = PdfRgbColor::create(0);
        self::assertInstanceOf(PdfRgbColor::class, $actual);
        self::assertSameColor($actual, 0, 0, 0);

        // 3296100
        $value = 50 << 0x10 | 75 << 0x08 | 100;
        $actual = PdfRgbColor::create($value);
        self::assertInstanceOf(PdfRgbColor::class, $actual);
        self::assertSameColor($actual, 50, 75, 100);

        $actual = PdfRgbColor::create('#FAB');
        self::assertInstanceOf(PdfRgbColor::class, $actual);
        self::assertSameColor($actual, 255, 170, 187);

        $actual = PdfRgbColor::create('#FFAABB');
        self::assertInstanceOf(PdfRgbColor::class, $actual);
        self::assertSameColor($actual, 255, 170, 187);
    }

    public function testDarkGray(): void
    {
        $actual = PdfRgbColor::darkGray();
        self::assertSameColor($actual, 169, 169, 169);
    }

    public function testDarkGreen(): void
    {
        $actual = PdfRgbColor::darkGreen();
        self::assertSameColor($actual, 0, 128, 0);
    }

    public function testDarkRed(): void
    {
        $actual = PdfRgbColor::darkRed();
        self::assertSameColor($actual, 128, 0, 0);
    }

    public function testEquals(): void
    {
        $source = PdfRgbColor::instance(10, 20, 30);
        self::assertTrue($source->equals($source));
        self::assertTrue($source->equals(PdfRgbColor::instance(10, 20, 30)));
        self::assertFalse($source->equals(PdfRgbColor::instance(1, 2, 3)));
        self::assertFalse($source->equals(PdfCmykColor::instance(0, 0, 0, 0)));
        self::assertFalse($source->equals(PdfGrayColor::black()));
    }

    public function testGreen(): void
    {
        $actual = PdfRgbColor::green();
        self::assertSameColor($actual, 0, 255, 0);
    }

    public function testInstance(): void
    {
        $color = PdfRgbColor::instance(10, 20, 30);
        self::assertSame(10, $color->red);
        self::assertSame(20, $color->green);
        self::assertSame(30, $color->blue);
    }

    public function testOutput(): void
    {
        $color = PdfRgbColor::white();
        $actual = $color->getOutput();
        self::assertSame('1.000 1.000 1.000 rg', $actual);

        $color = PdfRgbColor::black();
        $actual = $color->getOutput();
        self::assertSame('0.000 g', $actual);

        $color = PdfRgbColor::instance(10, 20, 30);
        $actual = $color->getOutput();
        self::assertSame('0.039 0.078 0.118 rg', $actual);
    }

    public function testRed(): void
    {
        $actual = PdfRgbColor::red();
        self::assertSameColor($actual, 255, 0, 0);
    }

    public function testToString(): void
    {
        $color = PdfRgbColor::instance(10, 20, 30);
        $actual = (string) $color;
        self::assertSame('PdfRgbColor(10,20,30)', $actual);
    }

    protected static function assertSameColor(PdfRgbColor $actual, int $red, int $green, int $blue): void
    {
        self::assertSame($red, $actual->red);
        self::assertSame($green, $actual->green);
        self::assertSame($blue, $actual->blue);
    }
}
