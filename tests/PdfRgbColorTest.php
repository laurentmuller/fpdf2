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

namespace fpdf\Tests;

use fpdf\Interfaces\PdfColorInterface;
use fpdf\PdfGrayColor;
use fpdf\PdfRgbColor;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PdfRgbColorTest extends TestCase
{
    public static function getEqualsColors(): \Generator
    {
        $color = PdfRgbColor::instance(0, 0, 0);
        yield [$color, PdfRgbColor::instance(0, 0, 0), true];
        yield [$color, PdfRgbColor::instance(0, 0, 1), false];
        yield [$color, PdfGrayColor::instance(0), false];
    }

    public function testColor(): void
    {
        $color = PdfRgbColor::instance(0, 128, 255);
        $actual = $color->getColor();
        self::assertSame('0.000 0.502 1.000 RG', $actual);
    }

    #[DataProvider('getEqualsColors')]
    public function testEquals(PdfRgbColor $color, PdfColorInterface $other, bool $expected): void
    {
        $actual = $color->equals($other);
        self::assertSame($expected, $actual);
    }

    public function testToString(): void
    {
        $color = PdfRgbColor::instance(0, 128, 255);
        $actual = (string) $color;
        self::assertSame('PdfRgbColor(0, 128, 255)', $actual);
    }
}
