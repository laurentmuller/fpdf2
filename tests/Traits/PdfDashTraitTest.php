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

use fpdf\Enums\PdfDestination;
use fpdf\PdfDocument;
use fpdf\PdfRectangle;
use fpdf\Traits\PdfDashTrait;
use PHPUnit\Framework\TestCase;

class PdfDashTraitTest extends TestCase
{
    public function testDashedRect(): void
    {
        $document = new class() extends PdfDocument {
            use PdfDashTrait;
        };
        $document->addPage();
        $document->dashedRect(10, 15, 62, 20, 2.0, 0.5);
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
    }

    public function testDashedRectangle(): void
    {
        $document = new class() extends PdfDocument {
            use PdfDashTrait;
        };
        $document->addPage();
        $rectangle = new PdfRectangle(10, 15, 62, 20);
        $document->dashedRectangle($rectangle, 2.0, 0.5);
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
    }

    public function testLine(): void
    {
        $document = new class() extends PdfDocument {
            use PdfDashTrait;
        };
        $document->addPage();
        $document->setLineWidth(0.5);
        $document->setDashPattern(3.0, 2.0);
        $document->line(10, 10, 72, 10);
        $document->resetDashPattern();
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
    }
}
