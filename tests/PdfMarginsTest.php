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

use fpdf\PdfMargins;
use PHPUnit\Framework\TestCase;

class PdfMarginsTest extends TestCase
{
    public function testAsArray(): void
    {
        $margins = PdfMargins::instance();
        $actual = $margins->asArray();
        self::assertSame([0.0, 0.0, 0.0, 0.0], $actual);
    }

    public function testEquals(): void
    {
        $actual = PdfMargins::instance();
        self::assertTrue($actual->equals($actual));
        $other = PdfMargins::instance();
        self::assertTrue($actual->equals($other));
    }

    public function testInstanceNotZero(): void
    {
        $actual = PdfMargins::instance(10.0, 20.0, 30.0, 40.0);
        self::assertSameValues($actual, 10.0, 20.0, 30.0, 40.0);
    }

    public function testInstanceZero(): void
    {
        $actual = PdfMargins::instance();
        self::assertSameValues($actual, 0.0, 0.0, 0.0, 0.0);
    }

    public function testNotEquals(): void
    {
        $actual = PdfMargins::instance();
        $other = PdfMargins::instance(10.0, 20.0, 30.0, 40.0);
        self::assertFalse($actual->equals($other));
    }

    public function testReset(): void
    {
        $actual = PdfMargins::instance(10.0, 20.0, 30.0, 40.0);
        self::assertSameValues($actual, 10.0, 20.0, 30.0, 40.0);
        $actual->reset();
        self::assertSameValues($actual, 0.0, 0.0, 0.0, 0.0);
    }

    public function testSetMargins(): void
    {
        $actual = PdfMargins::instance();
        $actual->setMargins(10.0);
        self::assertSameValues($actual, 10.0, 10.0, 10.0, 10.0);
    }

    protected static function assertSameValues(
        PdfMargins $actual,
        float $left,
        float $top,
        float $right,
        float $bottom
    ): void {
        self::assertSame($left, $actual->left);
        self::assertSame($top, $actual->top);
        self::assertSame($right, $actual->right);
        self::assertSame($bottom, $actual->bottom);
    }
}
