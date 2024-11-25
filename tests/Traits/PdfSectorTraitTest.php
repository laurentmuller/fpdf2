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

namespace fpdf\Tests\Traits;

use fpdf\Tests\fixture\PdfDocumentSector;
use PHPUnit\Framework\TestCase;

class PdfSectorTraitTest extends TestCase
{
    public function testEmpty(): void
    {
        $document = new PdfDocumentSector();
        $document->sector(100, 100, 0, 0, 90);
        self::assertSame(0, $document->getPage());
    }

    public function testNegativeBothAngles(): void
    {
        $document = new PdfDocumentSector();
        $document->addPage();
        $document->sector(100, 100, 50, -10, -90);
        self::assertSame(1, $document->getPage());
    }

    public function testNegativeEndAngle(): void
    {
        $document = new PdfDocumentSector();
        $document->addPage();
        $document->sector(100, 100, 50, 10, -90);
        self::assertSame(1, $document->getPage());
    }

    public function testNegativeStartAngle(): void
    {
        $document = new PdfDocumentSector();
        $document->addPage();
        $document->sector(100, 100, 50, -10, 90);
        self::assertSame(1, $document->getPage());
    }

    public function testRenderClockwise(): void
    {
        $document = new PdfDocumentSector();
        $document->addPage();
        $document->sector(100, 100, 50, 0, 90);
        self::assertSame(1, $document->getPage());
    }

    public function testRenderCounterClockwise(): void
    {
        $document = new PdfDocumentSector();
        $document->addPage();
        $document->sector(100, 100, 50, 0, 90, clockwise: false);
        self::assertSame(1, $document->getPage());
    }
}
