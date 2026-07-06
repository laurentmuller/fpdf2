<?php

/*
 * This file is part of the FPDF2 package.
 *
 * (c) bibi.nu <bibi@bibi.nu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace fpdf\Tests\Traits;

use fpdf\Color\PdfRgbColor;
use fpdf\Enums\PdfMove;
use fpdf\PdfException;
use fpdf\PdfRectangle;
use fpdf\Tests\Fixture\PdfDocumentRoundedRectangle;
use PHPUnit\Framework\TestCase;

final class PdfRoundedRectangleTraitTest extends TestCase
{
    public function testMoveBelow(): void
    {
        $document = new PdfDocumentRoundedRectangle();
        $document->addPage();
        $document->roundedRect(100, 50, 5, 10, 20, move: PdfMove::BELOW);
        self::assertSame(10.0, $document->getX());
        self::assertSame(70.0, $document->getY());
    }

    public function testMoveNewLine(): void
    {
        $document = new PdfDocumentRoundedRectangle();
        $document->addPage();
        $document->roundedRect(100, 50, 5, 10, 20, move: PdfMove::NEW_LINE);
        self::assertSame($document->getLeftMargin(), $document->getX());
        self::assertSame(70.0, $document->getY());
    }

    public function testMoveRight(): void
    {
        $document = new PdfDocumentRoundedRectangle();
        $document->addPage();
        $document->roundedRect(100, 50, 5, 10, 20);
        self::assertSame(110.0, $document->getX());
        self::assertSame(20.0, $document->getY());
    }

    public function testRoundedInvalidRadius(): void
    {
        $document = new PdfDocumentRoundedRectangle();
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid radius: 40, maximum allowed: 25.');
        $document->roundedRect(50, 100, 40);
    }

    public function testRoundedRadiusNotPositive(): void
    {
        $document = new PdfDocumentRoundedRectangle();
        self::expectException(PdfException::class);
        self::expectExceptionMessage('The radius must be positive, 0 given.');
        $document->roundedRect(50, 100, 0);
    }

    public function testRoundedRect(): void
    {
        $document = new PdfDocumentRoundedRectangle();
        $document->addPage();
        $document->setFillColor(PdfRgbColor::darkGray());
        $document->setDrawColor(PdfRgbColor::red());

        $document->setXY(20, 20);
        $document->setLineWidth(1.5);
        $document->roundedRect(
            width: 20,
            height: 20,
            radius: 5,
            move: PdfMove::BELOW
        );
        self::assertSame(20.0, $document->getX());
        self::assertSame(40.0, $document->getY());

        $document->setLineWidth(0.5);
        $document->roundedRect(
            width: 100,
            height: 10,
            radius: 5,
            move: PdfMove::BELOW
        );
        self::assertSame(20.0, $document->getX());
        self::assertSame(50.0, $document->getY());
    }

    public function testRoundedRectangle(): void
    {
        $rect = new PdfRectangle(10, 20, 100, 50);
        $document = new PdfDocumentRoundedRectangle();
        $document->addPage();
        $document->roundedRectangle($rect, 5);
        self::assertSame($rect->right(), $document->getX());
        self::assertSame($rect->y, $document->getY());
    }
}
