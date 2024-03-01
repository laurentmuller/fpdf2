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

namespace fpdf;

use PHPUnit\Framework\TestCase;

#[PHPUnit\Framework\Attributes\CoversClass(PdfRectangle::class)]
class PdfRectangleTest extends TestCase
{
    public function testAsArray(): void
    {
        $actual = PdfRectangle::instance(10, 20, 30, 40)->asArray();
        self::assertCount(4, $actual);
        self::assertSame([10.0, 20.0, 30.0, 40.0], $actual);
    }

    public function testBottom(): void
    {
        $expected = 30.0;
        $rect = new PdfRectangle(10, 10, 20, 20);
        $actual = $rect->bottom();
        self::assertSame($expected, $actual);
    }

    public function testConstructor(): void
    {
        $actual = new PdfRectangle(10, 20, 30, 40);
        self::assertSameValues($actual, 10, 20, 30, 40);
    }

    public function testContainsPoint(): void
    {
        $actual = new PdfRectangle(10, 10, 20, 20);

        $pt = new PdfPoint(10, 10);
        self::assertTrue($actual->containsPoint($pt));

        $pt = new PdfPoint(15, 15);
        self::assertTrue($actual->containsPoint($pt));

        $pt = new PdfPoint(29.999999, 29.999999);
        self::assertTrue($actual->containsPoint($pt));

        $pt = new PdfPoint(0, 0);
        self::assertFalse($actual->containsPoint($pt));

        $pt = new PdfPoint(30, 30);
        self::assertFalse($actual->containsPoint($pt));
    }

    public function testContainsXY(): void
    {
        $actual = new PdfRectangle(10, 10, 20, 20);
        self::assertTrue($actual->containsXY(10, 10));
        self::assertTrue($actual->containsXY(15, 15));
        self::assertTrue($actual->containsXY(29.999999, 29.999999));

        self::assertFalse($actual->containsXY(0, 0));
        self::assertFalse($actual->containsXY(30, 30));
    }

    public function testEquals(): void
    {
        $rect1 = PdfRectangle::instance(0, 0, 50, 100);
        $rect2 = PdfRectangle::instance(0, 0, 50, 100);

        self::assertTrue($rect1->equals($rect1));
        self::assertTrue($rect2->equals($rect2));

        self::assertTrue($rect1->equals($rect2));
        self::assertTrue($rect2->equals($rect1));

        $rect2 = PdfRectangle::instance(0, 10, 50, 100);
        self::assertFalse($rect1->equals($rect2));
        self::assertFalse($rect2->equals($rect1));
    }

    public function testGetOrigin(): void
    {
        $rect = new PdfRectangle(10, 20, 30, 40);
        $actual = $rect->getOrigin();
        self::assertSame(10.0, $actual->x);
        self::assertSame(20.0, $actual->y);
    }

    public function testGetSize(): void
    {
        $rect = new PdfRectangle(10, 20, 30, 40);
        $actual = $rect->getSize();
        self::assertSame(30.0, $actual->width);
        self::assertSame(40.0, $actual->height);
    }

    public function testHeight(): void
    {
        $expected = 20.0;
        $rect = new PdfRectangle(30, 10, 20, 20);
        $actual = $rect->height;
        self::assertSame($expected, $actual);
    }

    public function testIndent(): void
    {
        $actual = new PdfRectangle(10, 10, 20, 20);
        $actual->indent(5);
        self::assertSameValues($actual, 15, 10, 15, 20);
    }

    public function testIndentNegative(): void
    {
        $actual = new PdfRectangle(10, 10, 20, 20);
        $actual->indent(-10);
        self::assertSameValues($actual, 10, 10, 20, 20);
    }

    public function testIndentZero(): void
    {
        $actual = new PdfRectangle(10, 10, 20, 20);
        $actual->indent(0);
        self::assertSameValues($actual, 10, 10, 20, 20);
    }

    public function testInflate(): void
    {
        $actual = new PdfRectangle(0, 0, 10, 10);
        $actual->inflate(5);
        self::assertSameValues($actual, -5, -5, 20, 20);
    }

    public function testInflateX(): void
    {
        $actual = new PdfRectangle(0, 0, 10, 10);
        $actual->inflateX(5);
        self::assertSameValues($actual, -5, 0, 20, 10);
    }

    public function testInflateXY(): void
    {
        $actual = new PdfRectangle(0, 0, 10, 10);
        $actual->inflateXY(5, 5);
        self::assertSameValues($actual, -5, -5, 20, 20);
    }

    public function testInflateY(): void
    {
        $actual = new PdfRectangle(0, 0, 10, 10);
        $actual->inflateY(5);
        self::assertSameValues($actual, 0, -5, 10, 20);
    }

    public function testInstance(): void
    {
        $actual = PdfRectangle::instance(10, 20, 30, 40);
        self::assertSameValues($actual, 10, 20, 30, 40);
    }

    public function testIntersect(): void
    {
        $rect1 = new PdfRectangle(10, 10, 20, 20);
        $rect2 = new PdfRectangle(10, 10, 20, 20);
        self::assertTrue($rect1->intersect($rect2));
    }

    public function testRight(): void
    {
        $expected = 30.0;
        $rect = new PdfRectangle(10, 10, 20, 20);
        $actual = $rect->right();
        self::assertSame($expected, $actual);
    }

    public function testScale(): void
    {
        $rect = PdfRectangle::instance(10, 20, 50, 100);

        $actual = $rect->scale(2);
        self::assertSameValues($actual, 20, 40, 100, 200);

        $actual = $rect->scale(0.5);
        self::assertSameValues($actual, 5, 10, 25, 50);
    }

    public function testSetBottom(): void
    {
        $actual = new PdfRectangle(10, 10, 20, 20);
        $actual->setBottom(40);
        self::assertSameValues($actual, 10, 10, 20, 30);
    }

    public function testSetOrigin(): void
    {
        $actual = new PdfRectangle(0, 0, 100, 100);
        $actual->setOrigin(new PdfPoint(10, 20));
        self::assertSameValues($actual, 10, 20, 100, 100);
    }

    public function testSetRight(): void
    {
        $actual = new PdfRectangle(10, 10, 20, 20);
        $actual->setRight(40);
        self::assertSameValues($actual, 10, 10, 30, 20);
    }

    public function testSetSize(): void
    {
        $actual = new PdfRectangle(0, 0, 20, 20);
        $actual->setSize(new PdfSize(10, 10));
        self::assertSameValues($actual, 0, 0, 10, 10);
    }

    public function testUnion(): void
    {
        $rect1 = new PdfRectangle(0, 0, 20, 20);
        $rect2 = new PdfRectangle(10, 10, 20, 20);
        $actual = $rect1->union($rect2);
        self::assertSameValues($actual, 0, 0, 30, 30);
    }

    public function testWidth(): void
    {
        $expected = 20.0;
        $rect = new PdfRectangle(30, 10, 20, 20);
        $actual = $rect->width;
        self::assertSame($expected, $actual);
    }

    public function testX(): void
    {
        $expected = 30.0;
        $rect = new PdfRectangle(30, 10, 20, 20);
        $actual = $rect->x;
        self::assertSame($expected, $actual);
    }

    public function testY(): void
    {
        $expected = 30.0;
        $rect = new PdfRectangle(30, 30, 20, 20);
        $actual = $rect->y;
        self::assertSame($expected, $actual);
    }

    protected static function assertSameValues(PdfRectangle $actual, float $x, float $y, float $w, float $h): void
    {
        self::assertSame($x, $actual->x);
        self::assertSame($y, $actual->y);
        self::assertSame($w, $actual->width);
        self::assertSame($h, $actual->height);
    }
}
