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

use fpdf\Enums\PdfDestination;
use fpdf\Enums\PdfLayout;
use fpdf\Enums\PdfVersion;
use fpdf\Enums\PdfZoom;

class PdfDocDisplayModeTest extends AbstractPdfDocTestCase
{
    public function testDisplayDefault(): void
    {
        $doc = $this->createDocument();
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringNotContainsString('/PageLayout', $actual);
        self::assertStringNotContainsString('/OpenAction', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testOneColumn(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(layout: PdfLayout::ONE_COLUMN);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /OneColumn', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testSinglePage(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(layout: PdfLayout::SINGLE_PAGE);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /SinglePage', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testTwoColumnLeft(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(layout: PdfLayout::TWO_COLUMN_LEFT);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /TwoColumnLeft', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testTwoColumnRight(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(layout: PdfLayout::TWO_COLUMN_RIGHT);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /TwoColumnRight', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testTwoPageLeft(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(layout: PdfLayout::TWO_PAGE_LEFT);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /TwoPageLeft', $actual);
        self::assertSame(PdfVersion::VERSION_1_5, $doc->getPdfVersion());
    }

    public function testTwoPageRight(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(layout: PdfLayout::TWO_PAGE_RIGHT);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /TwoPageRight', $actual);
        self::assertSame(PdfVersion::VERSION_1_5, $doc->getPdfVersion());
    }

    public function testZoomFullPage(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(PdfZoom::FULL_PAGE);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/OpenAction', $actual);
        self::assertStringNotContainsString('/PageLayout', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testZoomFullWidth(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(PdfZoom::FULL_WIDTH);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/OpenAction', $actual);
        self::assertStringNotContainsString('/PageLayout', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testZoomReal(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(PdfZoom::REAL);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/OpenAction', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testZoomValue(): void
    {
        $doc = $this->createDocument();
        $doc->setDisplayMode(80);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/OpenAction', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }
}
