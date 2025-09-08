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
use PHPUnit\Framework\Attributes\DataProvider;

class PdfRgbColorTest extends AbstractColorTestCase
{
    /**
     * @return \Generator<int, array{0: ?string, 1: ?PdfRgbColor}>
     */
    public static function getCreatedColors(): \Generator
    {
        yield [null, null];
        yield ['', null];
        yield ['12', null];
        yield ['1234', null];

        $expected = PdfRgbColor::instance(255, 170, 187);
        yield ['FAB', $expected];
        yield ['#FAB', $expected];
        yield ['#FAB#', $expected];

        $expected = PdfRgbColor::instance(255, 0, 187);
        yield ['FF00BB', $expected];
        yield ['#FF00BB', $expected];
        yield ['#FF00BB#', $expected];
    }

    /**
     * @return \Generator<int, array{0: int<0, 255>, 1: int<0, 255>, 2: int<0, 255>, 3: string, 4?: string}>
     */
    public static function getHexColors(): \Generator
    {
        $rgb = [0x00, 0x00, 0x00];
        yield [$rgb[0], $rgb[1], $rgb[2], '000000'];
        yield [$rgb[0], $rgb[1], $rgb[2], '0x000000', '0x'];

        $rgb = [0xFF, 0xFF, 0xFF];
        yield [$rgb[0], $rgb[1], $rgb[2], 'ffffff'];
        yield [$rgb[0], $rgb[1], $rgb[2], '0xffffff', '0x'];

        $rgb = [0x32, 0x64, 0x96];
        yield [$rgb[0], $rgb[1], $rgb[2], '326496'];
        yield [$rgb[0], $rgb[1], $rgb[2], '0x326496', '0x'];

        $rgb = [0x00, 0x64, 0x96];
        yield [$rgb[0], $rgb[1], $rgb[2], '006496'];
        yield [$rgb[0], $rgb[1], $rgb[2], '0x006496', '0x'];

        $rgb = [0x00, 0xFF, 0x00];
        yield [$rgb[0], $rgb[1], $rgb[2], '00ff00'];
        yield [$rgb[0], $rgb[1], $rgb[2], '0x00ff00', '0x'];

        $rgb = [0x00, 0x00, 0xFF];
        yield [$rgb[0], $rgb[1], $rgb[2], '0000ff'];
        yield [$rgb[0], $rgb[1], $rgb[2], '0x0000ff', '0x'];
    }

    /**
     * @param int<0, 255> $red
     * @param int<0, 255> $green
     * @param int<0, 255> $blue
     */
    #[DataProvider('getHexColors')]
    public function testAsHex(int $red, int $green, int $blue, string $expected, string $prefix = ''): void
    {
        $color = PdfRgbColor::instance($red, $green, $blue);
        $actual = $color->asHex($prefix);
        self::assertSame($expected, $actual);
    }

    public function testBlack(): void
    {
        $actual = PdfRgbColor::black();
        self::assertSameRgbColor($actual, 0, 0, 0);
    }

    public function testBlue(): void
    {
        $actual = PdfRgbColor::blue();
        self::assertSameRgbColor($actual, 0, 0, 255);
    }

    #[DataProvider('getCreatedColors')]
    public function testCreate(?string $value, ?PdfRgbColor $expected): void
    {
        $actual = PdfRgbColor::create($value);
        self::assertEqualsCanonicalizing($expected, $actual);
    }

    public function testDarkGray(): void
    {
        $actual = PdfRgbColor::darkGray();
        self::assertSameRgbColor($actual, 169, 169, 169);
    }

    public function testDarkGreen(): void
    {
        $actual = PdfRgbColor::darkGreen();
        self::assertSameRgbColor($actual, 0, 128, 0);
    }

    public function testDarkRed(): void
    {
        $actual = PdfRgbColor::darkRed();
        self::assertSameRgbColor($actual, 128, 0, 0);
    }

    public function testEquals(): void
    {
        $source = PdfRgbColor::instance(10, 20, 30);
        self::assertEqualsColor($source, $source);
        self::assertEqualsColor($source, PdfRgbColor::instance(10, 20, 30));
        self::assertNotEqualsColor($source, PdfCmykColor::black());
        self::assertNotEqualsColor($source, PdfGrayColor::black());
        self::assertNotEqualsColor($source, PdfRgbColor::black());
    }

    public function testGreen(): void
    {
        $actual = PdfRgbColor::green();
        self::assertSameRgbColor($actual, 0, 255, 0);
    }

    public function testInstance(): void
    {
        $actual = PdfRgbColor::instance(10, 20, 30);
        self::assertSameRgbColor($actual, 10, 20, 30);
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
        self::assertSameRgbColor($actual, 255, 0, 0);
    }

    public function testToCmykColor(): void
    {
        $color = PdfRgbColor::instance(10, 20, 30);
        $actual = $color->toCmykColor();
        self::assertSameCmykColor($actual, 67, 33, 0, 88);
    }

    public function testToGrayColor(): void
    {
        $color = PdfRgbColor::instance(0, 0, 0);
        $actual = $color->toGrayColor();
        self::assertSameGrayColor($actual, 0);

        $color = PdfRgbColor::instance(255, 255, 255);
        $actual = $color->toGrayColor();
        self::assertSameGrayColor($actual, 255);

        $color = PdfRgbColor::instance(128, 128, 128);
        $actual = $color->toGrayColor();
        self::assertSameGrayColor($actual, 127);
    }

    public function testToString(): void
    {
        $color = PdfRgbColor::instance(10, 20, 30);
        $actual = (string) $color;
        self::assertSame('PdfRgbColor(10,20,30)', $actual);
    }
}
