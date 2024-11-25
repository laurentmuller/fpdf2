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

use fpdf\Enums\PdfDestination;
use fpdf\Enums\PdfZoom;

class PdfDocZoomTest extends AbstractPdfDocTestCase
{
    public function testDefault(): void
    {
        $doc = $this->createDocument();
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringNotContainsString('/OpenAction', $actual);
    }

    public function testFullPage(): void
    {
        $doc = $this->createDocument();
        $doc->setZoom(PdfZoom::FULL_PAGE);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/OpenAction', $actual);
        self::assertStringContainsString('/Fit', $actual);
    }

    public function testFullWidth(): void
    {
        $doc = $this->createDocument();
        $doc->setZoom(PdfZoom::FULL_WIDTH);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/OpenAction', $actual);
        self::assertStringContainsString('/FitH null', $actual);
    }

    public function testReal(): void
    {
        $doc = $this->createDocument();
        $doc->setZoom(PdfZoom::REAL);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/OpenAction', $actual);
        self::assertStringContainsString('/XYZ null null 1', $actual);
    }

    public function testWithValue(): void
    {
        $doc = $this->createDocument();
        $doc->setZoom(80);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/OpenAction', $actual);
        self::assertStringContainsString('/XYZ null null 0.80', $actual);
    }
}
