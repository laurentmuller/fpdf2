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

use fpdf\PdfSize;
use PHPUnit\Framework\TestCase;

#[PHPUnit\Framework\Attributes\CoversClass(PdfSize::class)]
class PdfSizeTest extends TestCase
{
    public function testAsArray(): void
    {
        $actual = PdfSize::instance(10, 15)->asArray();
        self::assertCount(2, $actual);
        self::assertArrayHasKey(0, $actual);
        self::assertArrayHasKey(1, $actual);
        self::assertSame([10.0, 15.0], $actual);
    }

    public function testConstructor(): void
    {
        $actual = new PdfSize(10, 15);
        self::assertSameValues($actual, 10, 15);
    }

    public function testEquals(): void
    {
        $size1 = PdfSize::instance(10, 15);
        $size2 = PdfSize::instance(10, 15);

        self::assertTrue($size1->equals($size1));
        self::assertTrue($size2->equals($size2));

        self::assertTrue($size1->equals($size2));
        self::assertTrue($size2->equals($size1));

        $size2 = PdfSize::instance(15, 15);
        self::assertFalse($size1->equals($size2));
        self::assertFalse($size2->equals($size1));
    }

    public function testInstance(): void
    {
        $actual = PdfSize::instance(10, 15);
        self::assertSameValues($actual, 10, 15);
    }

    public function testScale(): void
    {
        $actual = PdfSize::instance(10, 15)->scale(2.0);
        self::assertSameValues($actual, 20, 30);

        $actual = PdfSize::instance(10, 15)->scale(0.5);
        self::assertSameValues($actual, 5, 7.5);
    }

    public function testSwap(): void
    {
        $actual = PdfSize::instance(10, 15)->swap();
        self::assertSameValues($actual, 15, 10);
    }

    protected static function assertSameValues(PdfSize $actual, float $width, float $height): void
    {
        self::assertSame($width, $actual->width);
        self::assertSame($height, $actual->height);
    }
}
