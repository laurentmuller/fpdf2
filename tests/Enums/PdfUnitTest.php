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

namespace fpdf\Tests\Enums;

use fpdf\Enums\PdfUnit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PdfUnitTest extends TestCase
{
    /**
     * @psalm-return \Generator<int, array{float, PdfUnit, PdfUnit, float}>
     */
    public static function getConverts(): \Generator
    {
        $value = 123.123456;
        yield [$value, PdfUnit::CENTIMETER, PdfUnit::INCH, $value / 2.54];
        yield [$value, PdfUnit::CENTIMETER, PdfUnit::MILLIMETER, $value * 10.0];
        yield [$value, PdfUnit::CENTIMETER, PdfUnit::POINT, $value * 72.0 / 2.54];

        yield [$value, PdfUnit::MILLIMETER, PdfUnit::CENTIMETER, $value / 10.0];
        yield [$value, PdfUnit::MILLIMETER, PdfUnit::INCH, $value / 25.4];
        yield [$value, PdfUnit::MILLIMETER, PdfUnit::POINT, $value * 72.0 / 25.4];

        yield [$value, PdfUnit::POINT, PdfUnit::CENTIMETER, $value * 2.54 / 72.0];
        yield [$value, PdfUnit::POINT, PdfUnit::INCH, $value / 72.0];
        yield [$value, PdfUnit::POINT, PdfUnit::MILLIMETER, $value * 25.4 / 72.0];

        yield [$value, PdfUnit::INCH, PdfUnit::CENTIMETER, $value * 2.54];
        yield [$value, PdfUnit::INCH, PdfUnit::MILLIMETER, $value * 25.4];
        yield [$value, PdfUnit::INCH, PdfUnit::POINT, $value * 72.0];
    }

    /**
     * @psalm-return \Generator<int, array{PdfUnit, float}>
     */
    public static function getScaleFactors(): \Generator
    {
        yield [PdfUnit::CENTIMETER, 28.34];
        yield [PdfUnit::INCH, 72.0];
        yield [PdfUnit::MILLIMETER, 2.83];
        yield [PdfUnit::POINT, 1.0];
    }

    /**
     * @psalm-return \Generator<int, array{PdfUnit, string}>
     */
    public static function getValues(): \Generator
    {
        yield [PdfUnit::CENTIMETER, 'cm'];
        yield [PdfUnit::INCH, 'in'];
        yield [PdfUnit::MILLIMETER, 'mm'];
        yield [PdfUnit::POINT, 'pt'];
    }

    #[DataProvider('getConverts')]
    public function testConvert(float $value, PdfUnit $source, PdfUnit $target, float $expected): void
    {
        $actual = $source->convert($value, $target);
        self::assertEqualsWithDelta($expected, $actual, 0.0001);
    }

    public function testConvertSameUnit(): void
    {
        $value = 123.123456;
        $source = PdfUnit::CENTIMETER;
        $target = PdfUnit::CENTIMETER;
        $actual = $source->convert($value, $target);
        self::assertSame($value, $actual);
    }

    #[DataProvider('getScaleFactors')]
    public function testScaleFactor(PdfUnit $unit, float $expected): void
    {
        $actual = $unit->getScaleFactor();
        self::assertEqualsWithDelta($expected, $actual, 0.01);
    }

    #[DataProvider('getValues')]
    public function testValue(PdfUnit $unit, string $expected): void
    {
        $actual = $unit->value;
        self::assertSame($expected, $actual);
    }
}
