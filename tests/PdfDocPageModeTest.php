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

use fpdf\AbstractPdfDocTestCase;
use fpdf\Enums\PdfDestination;
use fpdf\Enums\PdfPageMode;
use fpdf\Enums\PdfVersion;

class PdfDocPageModeTest extends AbstractPdfDocTestCase
{
    public function testDefault(): void
    {
        $doc = $this->createDocument();
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringNotContainsString('/PageMode', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testFullScreen(): void
    {
        $doc = $this->createDocument();
        $doc->setPageMode(PdfPageMode::FULL_SCREEN);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageMode /FullScreen', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testUseAttachments(): void
    {
        $doc = $this->createDocument();
        $doc->setPageMode(PdfPageMode::USE_ATTACHMENTS);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageMode /UseAttachments', $actual);
        self::assertSame(PdfVersion::VERSION_1_6, $doc->getPdfVersion());
    }

    public function testUseNone(): void
    {
        $doc = $this->createDocument();
        $doc->setPageMode(PdfPageMode::USE_NONE);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringNotContainsString('/PageMode', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testUseOc(): void
    {
        $doc = $this->createDocument();
        $doc->setPageMode(PdfPageMode::USE_OC);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageMode /UseOC', $actual);
        self::assertSame(PdfVersion::VERSION_1_5, $doc->getPdfVersion());
    }

    public function testUseOutlines(): void
    {
        $doc = $this->createDocument();
        $doc->setPageMode(PdfPageMode::USE_OUTLINES);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageMode /UseOutlines', $actual);
        self::assertSame(PdfVersion::VERSION_1_3, $doc->getPdfVersion());
    }

    public function testUseThumbs(): void
    {
        $doc = $this->createDocument();
        $doc->setPageMode(PdfPageMode::USE_THUMBS);
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('/PageMode /UseThumbs', $actual);
    }
}
