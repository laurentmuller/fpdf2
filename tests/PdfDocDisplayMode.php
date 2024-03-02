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

namespace fpdf;

class PdfDocDisplayMode extends AbstractPdfDocTestCase
{
    public function testDisplayContinuous(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(layout: PdfLayout::CONTINUOUS);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString($actual, '/PageLayout');
    }

    public function testDisplaySingle(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(layout: PdfLayout::SINGLE);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString($actual, '/PageLayout');
    }

    public function testDisplayTwoPages(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(layout: PdfLayout::TWO_PAGES);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString($actual, '/PageLayout');
    }

    public function testZoomFullPage(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(PdfZoom::FULL_PAGE);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString($actual, '/OpenAction');
    }

    public function testZoomFullWidth(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(PdfZoom::FULL_WIDTH);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString($actual, '/OpenAction');
    }

    public function testZoomReal(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(PdfZoom::REAL);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString($actual, '/OpenAction');
    }

    public function testZoomValue(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(80);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString($actual, '/OpenAction');
    }
}
