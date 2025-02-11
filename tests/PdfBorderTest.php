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

class PdfBorderTest extends TestCase
{
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
        self::assertTrue($actual->isLeft());
        self::assertTrue($actual->isRight());
        self::assertTrue($actual->isTop());
        self::assertTrue($actual->isBottom());
    }

    public function testBottom(): void
    {
        $actual = PdfBorder::bottom();
        self::assertFalse($actual->isLeft());
        self::assertFalse($actual->isRight());
        self::assertFalse($actual->isTop());
        self::assertTrue($actual->isBottom());
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

    public function testIsAll(): void
    {
        $actual = PdfBorder::none();
        self::assertFalse($actual->isAll());
        $actual = PdfBorder::all();
        self::assertTrue($actual->isAll());
        $actual = new PdfBorder(true, true, true, true);
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
        self::assertTrue($actual->isLeft());
        self::assertFalse($actual->isRight());
        self::assertFalse($actual->isTop());
        self::assertFalse($actual->isBottom());
    }

    public function testLeftRight(): void
    {
        $actual = PdfBorder::leftRight();
        self::assertTrue($actual->isLeft());
        self::assertTrue($actual->isRight());
        self::assertFalse($actual->isTop());
        self::assertFalse($actual->isBottom());
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
        self::assertFalse($actual->isLeft());
        self::assertFalse($actual->isRight());
        self::assertFalse($actual->isTop());
        self::assertFalse($actual->isBottom());
    }

    public function testOr(): void
    {
        $actual = PdfBorder::none()->or(PdfBorder::leftRight());
        self::assertTrue($actual->isLeft());
        self::assertTrue($actual->isRight());
        self::assertFalse($actual->isTop());
        self::assertFalse($actual->isBottom());
    }

    public function testRight(): void
    {
        $actual = PdfBorder::right();
        self::assertFalse($actual->isLeft());
        self::assertTrue($actual->isRight());
        self::assertFalse($actual->isTop());
        self::assertFalse($actual->isBottom());
    }

    public function testSetBottom(): void
    {
        $actual = PdfBorder::none();
        self::assertFalse($actual->isBottom());
        $actual->setBottom(false);
        self::assertFalse($actual->isBottom());
        $actual->setBottom();
        self::assertTrue($actual->isBottom());
    }

    public function testSetLeft(): void
    {
        $actual = PdfBorder::none();
        self::assertFalse($actual->isLeft());
        $actual->setLeft(false);
        self::assertFalse($actual->isLeft());
        $actual->setLeft();
        self::assertTrue($actual->isLeft());
    }

    public function testSetRight(): void
    {
        $actual = PdfBorder::none();
        self::assertFalse($actual->isRight());
        $actual->setRight(false);
        self::assertFalse($actual->isRight());
        $actual->setRight();
        self::assertTrue($actual->isRight());
    }

    public function testSetTop(): void
    {
        $actual = PdfBorder::none();
        self::assertFalse($actual->isTop());
        $actual->setTop(false);
        self::assertFalse($actual->isTop());
        $actual->setTop();
        self::assertTrue($actual->isTop());
    }

    public function testTop(): void
    {
        $actual = PdfBorder::top();
        self::assertFalse($actual->isLeft());
        self::assertFalse($actual->isRight());
        self::assertTrue($actual->isTop());
        self::assertFalse($actual->isBottom());
    }

    public function testTopBottom(): void
    {
        $actual = PdfBorder::topBottom();
        self::assertFalse($actual->isLeft());
        self::assertFalse($actual->isRight());
        self::assertTrue($actual->isTop());
        self::assertTrue($actual->isBottom());
    }
}
