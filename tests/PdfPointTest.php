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

use fpdf\PdfPoint;
use PHPUnit\Framework\TestCase;

#[PHPUnit\Framework\Attributes\CoversClass(PdfPoint::class)]
class PdfPointTest extends TestCase
{
    public function testAsArray(): void
    {
        $actual = PdfPoint::instance(10, 15)->asArray();
        self::assertCount(2, $actual);
        self::assertArrayHasKey(0, $actual);
        self::assertArrayHasKey(1, $actual);
        self::assertSame([10.0, 15.0], $actual);
    }

    public function testConstructor(): void
    {
        $actual = new PdfPoint(10, 15);
        self::assertSameValues($actual, 10, 15);
    }

    public function testEquals(): void
    {
        $pt1 = PdfPoint::instance(10, 15);
        $pt2 = PdfPoint::instance(10, 15);

        self::assertTrue($pt1->equals($pt1));
        self::assertTrue($pt2->equals($pt2));

        self::assertTrue($pt1->equals($pt2));
        self::assertTrue($pt2->equals($pt1));

        $pt2 = PdfPoint::instance(15, 15);
        self::assertFalse($pt1->equals($pt2));
        self::assertFalse($pt2->equals($pt1));
    }

    public function testInstance(): void
    {
        $actual = PdfPoint::instance(10, 15);
        self::assertSameValues($actual, 10, 15);
    }

    public function testScale(): void
    {
        $actual = PdfPoint::instance(10, 15)->scale(2.0);
        self::assertSameValues($actual, 20, 30);

        $actual = PdfPoint::instance(10, 15)->scale(0.5);
        self::assertSameValues($actual, 5, 7.5);
    }

    public function testSwap(): void
    {
        $actual = PdfPoint::instance(10, 15)->swap();
        self::assertSameValues($actual, 15, 10);
    }

    protected static function assertSameValues(PdfPoint $actual, float $x, float $y): void
    {
        self::assertSame($x, $actual->x);
        self::assertSame($y, $actual->y);
    }
}
