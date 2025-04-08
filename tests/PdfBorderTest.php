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
    /**
     * @psalm-return \Generator<int, array{0: PdfBorder}>
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
        self::assertTrue($actual->left);
        self::assertTrue($actual->right);
        self::assertTrue($actual->top);
        self::assertTrue($actual->bottom);
    }

    public function testBottom(): void
    {
        $actual = PdfBorder::bottom();
        self::assertFalse($actual->left);
        self::assertFalse($actual->right);
        self::assertFalse($actual->top);
        self::assertTrue($actual->bottom);
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
        self::assertTrue($actual->left);
        self::assertFalse($actual->right);
        self::assertFalse($actual->top);
        self::assertFalse($actual->bottom);
    }

    public function testLeftRight(): void
    {
        $actual = PdfBorder::leftRight();
        self::assertTrue($actual->left);
        self::assertTrue($actual->right);
        self::assertFalse($actual->top);
        self::assertFalse($actual->bottom);
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
        self::assertFalse($actual->left);
        self::assertFalse($actual->right);
        self::assertFalse($actual->top);
        self::assertFalse($actual->bottom);
    }

    public function testNotBottom(): void
    {
        $actual = PdfBorder::notBottom();
        self::assertTrue($actual->left);
        self::assertTrue($actual->right);
        self::assertTrue($actual->top);
        self::assertFalse($actual->bottom);
    }

    public function testNotLeft(): void
    {
        $actual = PdfBorder::notLeft();
        self::assertFalse($actual->left);
        self::assertTrue($actual->right);
        self::assertTrue($actual->top);
        self::assertTrue($actual->bottom);
    }

    public function testNotRight(): void
    {
        $actual = PdfBorder::notRight();
        self::assertTrue($actual->left);
        self::assertFalse($actual->right);
        self::assertTrue($actual->top);
        self::assertTrue($actual->bottom);
    }

    public function testNotTop(): void
    {
        $actual = PdfBorder::notTop();
        self::assertTrue($actual->left);
        self::assertTrue($actual->right);
        self::assertFalse($actual->top);
        self::assertTrue($actual->bottom);
    }

    public function testOr(): void
    {
        $actual = PdfBorder::none()->or(PdfBorder::leftRight());
        self::assertTrue($actual->left);
        self::assertTrue($actual->right);
        self::assertFalse($actual->top);
        self::assertFalse($actual->bottom);
    }

    public function testRight(): void
    {
        $actual = PdfBorder::right();
        self::assertFalse($actual->left);
        self::assertTrue($actual->right);
        self::assertFalse($actual->top);
        self::assertFalse($actual->bottom);
    }

    public function testTop(): void
    {
        $actual = PdfBorder::top();
        self::assertFalse($actual->left);
        self::assertFalse($actual->right);
        self::assertTrue($actual->top);
        self::assertFalse($actual->bottom);
    }

    public function testTopBottom(): void
    {
        $actual = PdfBorder::topBottom();
        self::assertFalse($actual->left);
        self::assertFalse($actual->right);
        self::assertTrue($actual->top);
        self::assertTrue($actual->bottom);
    }
}
