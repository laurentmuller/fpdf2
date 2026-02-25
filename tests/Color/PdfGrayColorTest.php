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

final class PdfGrayColorTest extends AbstractColorTestCase
{
    public function testBlack(): void
    {
        $actual = PdfGrayColor::black();
        self::assertSameGrayColor($actual, 0);
    }

    public function testEquals(): void
    {
        $source = PdfGrayColor::instance(50);
        self::assertEqualsColor($source, $source);
        self::assertEqualsColor($source, PdfGrayColor::instance(50));
        self::assertNotEqualsColor($source, PdfCmykColor::black());
        self::assertNotEqualsColor($source, PdfGrayColor::black());
        self::assertNotEqualsColor($source, PdfRgbColor::black());
    }

    public function testInstance(): void
    {
        $actual = PdfGrayColor::instance(125);
        self::assertSameGrayColor($actual, 125);
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

    public function testToCmykColor(): void
    {
        $color = PdfGrayColor::instance(128);
        $actual = $color->toCmykColor();
        self::assertSameCmykColor($actual, 0, 0, 0, 50);
    }

    public function testToGrayColor(): void
    {
        $color = PdfGrayColor::instance(128);
        $actual = $color->toGrayColor();
        self::assertSame($actual, $color);
    }

    public function testToRgbColor(): void
    {
        $color = PdfGrayColor::instance(128);
        $actual = $color->toRgbColor();
        self::assertSameRgbColor($actual, 128, 128, 128);
    }

    public function testToString(): void
    {
        $color = PdfGrayColor::white();
        $actual = (string) $color;
        self::assertSame('PdfGrayColor(255)', $actual);
    }

    public function testWhite(): void
    {
        $actual = PdfGrayColor::white();
        self::assertSameGrayColor($actual, 255);
    }
}
