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

use fpdf\Enums\PdfMove;
use fpdf\PdfException;
use fpdf\PdfRectangle;
use fpdf\Tests\Fixture\PdfDocumentRoundedRectangle;
use PHPUnit\Framework\TestCase;

final class PdfRoundedRectangleTraitTest extends TestCase
{
    public function testMoveBelow(): void
    {
        $document = $this->createDocument();
        $document->roundedRect(100, 50, 5, 10, 20, move: PdfMove::BELOW);
        self::assertSame(10.0, $document->getX());
        self::assertSame(70.0, $document->getY());
    }

    public function testMoveNewLine(): void
    {
        $document = $this->createDocument();
        $document->roundedRect(100, 50, 5, 10, 20, move: PdfMove::NEW_LINE);
        self::assertSame($document->getLeftMargin(), $document->getX());
        self::assertSame(70.0, $document->getY());
    }

    public function testMoveRight(): void
    {
        $document = $this->createDocument();
        $document->roundedRect(100, 50, 5, 10, 20);
        self::assertSame(110.0, $document->getX());
        self::assertSame(20.0, $document->getY());
    }

    public function testRoundedRadiusNotPositive(): void
    {
        $document = $this->createDocument();
        self::expectException(PdfException::class);
        self::expectExceptionMessage('The radius must be positive, 0 given.');
        $document->roundedRect(50, 100, 0);
    }

    public function testRoundedRectangle(): void
    {
        $rect = new PdfRectangle(10, 20, 100, 50);
        $document = $this->createDocument();
        $document->roundedRectangle($rect, 5);
        self::assertSame($rect->right(), $document->getX());
        self::assertSame($rect->y, $document->getY());
    }

    private function createDocument(): PdfDocumentRoundedRectangle
    {
        return (new PdfDocumentRoundedRectangle())->addPage();
    }
}
