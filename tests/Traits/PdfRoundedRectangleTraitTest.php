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
    public function testRoundedInvalidRadius(): void
    {
        $document = new PdfDocumentRoundedRectangle();
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid radius: 40, maximum allowed: 25.');
        $document->roundedRect(0, 0, 50, 100, 40);
    }

    public function testRoundedRadiusNotPositive(): void
    {
        $document = new PdfDocumentRoundedRectangle();
        self::expectException(PdfException::class);
        self::expectExceptionMessage('The radius must be positive, 0 given.');
        $document->roundedRect(0, 0, 50, 100, 0);
    }

    public function testRoundedRect(): void
    {
        $document = new PdfDocumentRoundedRectangle();
        $document->addPage();
        $document->setFillColor(PdfRgbColor::darkGray());
        $document->setDrawColor(PdfRgbColor::red());

        $x = $document->getX();
        $y = $document->getY();
        $document->setLineWidth(1.5);
        $document->roundedRect(
            x: $x,
            y: $y,
            width: 20,
            height: 20,
            radius: 5,
            move: PdfMove::BELOW
        );

        $x = $document->getX();
        $y = $document->getY() + 10;
        $document->setLineWidth(0.5);
        $document->roundedRect(
            x: $x,
            y: $y,
            width: 100,
            height: 10,
            radius: 5,
            move: PdfMove::NEW_LINE
        );
        self::assertSame(1, $document->getPage());
    }

    public function testRoundedRectangle(): void
    {
        $rect = new PdfRectangle(10, 20, 100, 50);
        $document = new PdfDocumentRoundedRectangle();
        $document->addPage();
        $document->roundedRectangle($rect, 5);
        self::assertSame(1, $document->getPage());
    }
}
