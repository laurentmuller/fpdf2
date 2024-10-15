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
use fpdf\Enums\PdfLayout;
use fpdf\Enums\PdfVersion;

class PdfDocLayoutTest extends AbstractPdfDocTestCase
{
    public function testDefault(): void
    {
        $doc = $this->createDocument();
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringNotContainsString('/OpenAction', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testOneColumn(): void
    {
        $doc = $this->createDocument();
        $doc->setLayout(PdfLayout::ONE_COLUMN);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /OneColumn', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testSinglePage(): void
    {
        $doc = $this->createDocument();
        $doc->setLayout(PdfLayout::SINGLE_PAGE);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /SinglePage', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testTwoColumnLeft(): void
    {
        $doc = $this->createDocument();
        $doc->setLayout(PdfLayout::TWO_COLUMN_LEFT);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /TwoColumnLeft', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testTwoColumnRight(): void
    {
        $doc = $this->createDocument();
        $doc->setLayout(PdfLayout::TWO_COLUMN_RIGHT);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /TwoColumnRight', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testTwoPageLeft(): void
    {
        $doc = $this->createDocument();
        $doc->setLayout(PdfLayout::TWO_PAGE_LEFT);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /TwoPageLeft', $actual);
        self::assertSame(PdfVersion::VERSION_1_5, $doc->getPdfVersion());
    }

    public function testTwoPageRight(): void
    {
        $doc = $this->createDocument();
        $doc->setLayout(PdfLayout::TWO_PAGE_RIGHT);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageLayout /TwoPageRight', $actual);
        self::assertSame(PdfVersion::VERSION_1_5, $doc->getPdfVersion());
    }
}
