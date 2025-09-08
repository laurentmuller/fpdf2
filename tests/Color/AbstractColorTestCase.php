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
use fpdf\Interfaces\PdfColorInterface;
use PHPUnit\Framework\TestCase;

abstract class AbstractColorTestCase extends TestCase
{
    protected static function assertEqualsColor(PdfColorInterface $source, PdfColorInterface $target): void
    {
        self::assertTrue($source->equals($target));
    }

    protected static function assertNotEqualsColor(PdfColorInterface $source, PdfColorInterface $target): void
    {
        self::assertFalse($source->equals($target));
    }

    protected static function assertSameCmykColor(
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

    protected static function assertSameGrayColor(PdfGrayColor $actual, int $level): void
    {
        self::assertSame($level, $actual->level);
    }

    protected static function assertSameRgbColor(PdfRgbColor $actual, int $red, int $green, int $blue): void
    {
        self::assertSame($red, $actual->red);
        self::assertSame($green, $actual->green);
        self::assertSame($blue, $actual->blue);
    }
}
