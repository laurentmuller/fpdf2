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

use fpdf\Enums\PdfFontName;
use fpdf\PdfBorder;
use fpdf\PdfDocument;
use fpdf\PdfRectangle;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PdfBorderTest extends TestCase
{
    /**
     * @phpstan-return \Generator<int, array{PdfBorder}>
     */
    public static function getBorders(): \Generator
    {
        yield [PdfBorder::none()];
        yield [PdfBorder::all()];
        yield [PdfBorder::left()];
        yield [PdfBorder::right()];
        yield [PdfBorder::top()];
        yield [PdfBorder::bottom()];
    }

    public function testAll(): void
    {
        $actual = PdfBorder::all();
        self::assertTrue($actual->isAll());
        self::assertFalse($actual->isNone());
        $this->assertBorderEquals($actual, true, true, true, true);
    }

    public function testBottom(): void
    {
        $actual = PdfBorder::bottom();
        $this->assertBorderEquals($actual, false, false, false, true);
    }

    #[DataProvider('getBorders')]
    public function testDraw(PdfBorder $border): void
    {
        $document = new PdfDocument();
        $document->setFont(PdfFontName::ARIAL)
            ->addPage();
        $bounds = new PdfRectangle(10, 10, 100, 100);
        $border->draw($document, $bounds);
        self::assertSame(1, $document->getPage());
    }

    public function testEquals(): void
    {
        $actual = PdfBorder::all();
        self::assertTrue($actual->equals($actual));
        $other = PdfBorder::all();
        self::assertTrue($actual->equals($other));
        $other = PdfBorder::none();
        self::assertFalse($actual->equals($other));
    }

    public function testInstance(): void
    {
        $actual = PdfBorder::instance(true, true, true, true);
        self::assertTrue($actual->isAll());
    }

    public function testIsAll(): void
    {
        $actual = PdfBorder::none();
        self::assertFalse($actual->isAll());
        $actual = PdfBorder::all();
        self::assertTrue($actual->isAll());
        $actual = PdfBorder::instance(true, true, true, true);
        self::assertTrue($actual->isAll());
    }

    public function testIsAny(): void
    {
        $actual = PdfBorder::none();
        self::assertFalse($actual->isAny());
        $actual = PdfBorder::all();
        self::assertTrue($actual->isAny());
        $actual = PdfBorder::left();
        self::assertTrue($actual->isAny());
    }

    public function testLeft(): void
    {
        $actual = PdfBorder::left();
        $this->assertBorderEquals($actual, true, false, false, false);
    }

    public function testLeftRight(): void
    {
        $actual = PdfBorder::leftRight();
        $this->assertBorderEquals($actual, true, false, true, false);
    }

    public function testMerge(): void
    {
        $border1 = PdfBorder::topBottom();
        $border2 = PdfBorder::leftRight();
        $actual = PdfBorder::merge($border1, $border2);
        self::assertTrue($actual->isAll());
    }

    public function testNone(): void
    {
        $actual = PdfBorder::none();
        $this->assertBorderEquals($actual, false, false, false, false);
    }

    public function testNotBottom(): void
    {
        $actual = PdfBorder::notBottom();
        $this->assertBorderEquals($actual, true, true, true, false);
    }

    public function testNotLeft(): void
    {
        $actual = PdfBorder::notLeft();
        $this->assertBorderEquals($actual, false, true, true, true);
    }

    public function testNotRight(): void
    {
        $actual = PdfBorder::notRight();
        $this->assertBorderEquals($actual, true, true, false, true);
    }

    public function testNotTop(): void
    {
        $actual = PdfBorder::notTop();
        $this->assertBorderEquals($actual, true, false, true, true);
    }

    public function testOr(): void
    {
        $actual = PdfBorder::none()->or(PdfBorder::leftRight());
        $this->assertBorderEquals($actual, true, false, true, false);
    }

    public function testRight(): void
    {
        $actual = PdfBorder::right();
        $this->assertBorderEquals($actual, false, false, true, false);
    }

    public function testTop(): void
    {
        $actual = PdfBorder::top();
        $this->assertBorderEquals($actual, false, true, false, false);
    }

    public function testTopBottom(): void
    {
        $actual = PdfBorder::topBottom();
        $this->assertBorderEquals($actual, false, true, false, true);
    }

    private function assertBorderEquals(
        PdfBorder $actual,
        bool $expectedLeft,
        bool $expectedTop,
        bool $expectedRight,
        bool $expectedBottom,
    ): void {
        self::assertSame($expectedLeft, $actual->left);
        self::assertSame($expectedTop, $actual->top);
        self::assertSame($expectedRight, $actual->right);
        self::assertSame($expectedBottom, $actual->bottom);
    }
}
