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

class PdfGrayColorTest extends TestCase
{
    public static function getEqualsColors(): \Generator
    {
        $color = PdfGrayColor::instance(128);
        yield [$color, PdfGrayColor::instance(128), true];
        yield [$color, PdfGrayColor::instance(129), false];
        yield [$color, new PdfRgbColor(100, 0, 0), false];
    }

    public function testColor(): void
    {
        $color = PdfGrayColor::instance(128);
        $actual = $color->getColor();
        self::assertSame('0.502 G', $actual);
    }

    #[DataProvider('getEqualsColors')]
    public function testEquals(PdfGrayColor $color, PdfColorInterface $other, bool $expected): void
    {
        $actual = $color->equals($other);
        self::assertSame($expected, $actual);
    }

    public function testToString(): void
    {
        $color = PdfGrayColor::instance(128);
        $actual = (string) $color;
        self::assertSame('PdfGrayColor(128)', $actual);
    }
}
