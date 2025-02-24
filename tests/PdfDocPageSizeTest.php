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

use fpdf\Enums\PdfOrientation;
use fpdf\Enums\PdfPageSize;
use fpdf\PdfDocument;
use fpdf\PdfSize;

class PdfDocPageSizeTest extends AbstractPdfDocTestCase
{
    public function testDefaultPageSize(): void
    {
        $doc = new PdfDocument();
        $actual = $doc->getPageSize();
        self::assertEqualsWithDelta(210.0, $actual->width, 0.01);
        self::assertEqualsWithDelta(297.0, $actual->height, 0.01);
    }

    public function testWithA5PageSize(): void
    {
        $doc = new PdfDocument(size: PdfPageSize::A5);
        $actual = $doc->getPageSize();
        self::assertEqualsWithDelta(148.0, $actual->width, 0.01);
        self::assertEqualsWithDelta(210.0, $actual->height, 0.01);
    }

    public function testWithLandscapePageSize(): void
    {
        $doc = new PdfDocument(orientation: PdfOrientation::LANDSCAPE);
        $actual = $doc->getPageSize();
        self::assertEqualsWithDelta(297.0, $actual->width, 0.01);
        self::assertEqualsWithDelta(210.0, $actual->height, 0.01);
    }

    public function testWithSize(): void
    {
        $size = PdfSize::instance(100.0, 200.0);
        $doc = new PdfDocument(size: $size);
        $actual = $doc->getPageSize();
        self::assertEqualsWithDelta(100.0, $actual->width, 0.01);
        self::assertEqualsWithDelta(200.0, $actual->height, 0.01);
    }
}
