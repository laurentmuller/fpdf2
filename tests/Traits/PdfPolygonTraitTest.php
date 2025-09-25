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

use fpdf\Color\PdfRgbColor;
use fpdf\Enums\PdfDestination;
use fpdf\Enums\PdfRectangleStyle;
use fpdf\PdfPoint;
use fpdf\Tests\Fixture\PdfDocumentPolygon;
use PHPUnit\Framework\TestCase;

class PdfPolygonTraitTest extends TestCase
{
    public function testRender(): void
    {
        $points = [
            new PdfPoint(50, 115),
            new PdfPoint(150, 115),
            new PdfPoint(100, 20),
        ];

        $document = new PdfDocumentPolygon();
        $document->addPage();
        $document->setLineWidth(2.0);
        $document->setDrawColor(PdfRgbColor::red());
        $document->setFillColor(PdfRgbColor::blue());
        $document->polygon($points, PdfRectangleStyle::BOTH);
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
    }
}
