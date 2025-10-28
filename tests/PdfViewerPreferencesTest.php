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

use fpdf\Enums\PdfDirection;
use fpdf\Enums\PdfDuplex;
use fpdf\Enums\PdfNonFullScreenPageMode;
use fpdf\Enums\PdfScaling;
use fpdf\Enums\PdfVersion;
use fpdf\PdfViewerPreferences;
use PHPUnit\Framework\TestCase;

final class PdfViewerPreferencesTest extends TestCase
{
    public function testDefaultDirection(): void
    {
        $actual = PdfDirection::getDefault();
        self::assertSame(PdfDirection::L2R, $actual);
    }

    public function testDefaultDuplex(): void
    {
        $actual = PdfDuplex::getDefault();
        self::assertSame(PdfDuplex::NONE, $actual);
    }

    public function testDefaultEnum(): void
    {
        self::assertTrue(PdfDuplex::NONE->isDefault());
        self::assertTrue(PdfDirection::L2R->isDefault());
        self::assertTrue(PdfScaling::APP_DEFAULT->isDefault());
        self::assertTrue(PdfNonFullScreenPageMode::USE_NONE->isDefault());
    }

    public function testDefaultNonFullScreenPageMode(): void
    {
        $actual = PdfNonFullScreenPageMode::getDefault();
        self::assertSame(PdfNonFullScreenPageMode::USE_NONE, $actual);
    }

    public function testDefaultScaling(): void
    {
        $actual = PdfScaling::getDefault();
        self::assertSame(PdfScaling::APP_DEFAULT, $actual);
    }

    public function testDefaultValues(): void
    {
        $preferences = new PdfViewerPreferences();
        self::assertFalse($preferences->isCenterWindow());
        self::assertFalse($preferences->isDisplayDocTitle());
        self::assertFalse($preferences->isFitWindow());
        self::assertFalse($preferences->isHideMenubar());
        self::assertFalse($preferences->isHideToolbar());
        self::assertFalse($preferences->isHideWindowUI());
        self::assertFalse($preferences->isPickTrayByPDFSize());

        self::assertSame(PdfDuplex::NONE, $preferences->getDuplex());
        self::assertSame(PdfDirection::L2R, $preferences->getDirection());
        self::assertSame(PdfScaling::APP_DEFAULT, $preferences->getScaling());
        self::assertSame(PdfNonFullScreenPageMode::USE_NONE, $preferences->getNonFullScreenPageMode());
    }

    public function testFitWindow(): void
    {
        $preferences = new PdfViewerPreferences();
        $preferences->setFitWindow();
        $actual = $preferences->getOutput();
        self::assertStringContainsString('/FitWindow true', $actual);
    }

    public function testNoChange(): void
    {
        $preferences = new PdfViewerPreferences();
        $actual = $preferences->getOutput();
        self::assertSame('', $actual);
    }

    public function testPdfVersion(): void
    {
        $preferences = new PdfViewerPreferences();
        $actual = $preferences->getVersion();
        self::assertSame(PdfVersion::VERSION_1_3, $actual);

        $preferences->setDisplayDocTitle();
        $actual = $preferences->getVersion();
        self::assertSame(PdfVersion::VERSION_1_4, $actual);

        $preferences->setScaling(PdfScaling::NONE);
        $actual = $preferences->getVersion();
        self::assertSame(PdfVersion::VERSION_1_6, $actual);

        $preferences->setDuplex(PdfDuplex::DUPLEX_FLIP_LONG_EDGE);
        $actual = $preferences->getVersion();
        self::assertSame(PdfVersion::VERSION_1_7, $actual);

        $preferences->setPickTrayByPDFSize();
        $actual = $preferences->getVersion();
        self::assertSame(PdfVersion::VERSION_1_7, $actual);
    }

    public function testReset(): void
    {
        $preferences = new PdfViewerPreferences();
        $preferences->setCenterWindow();
        $actual = $preferences->getOutput();
        self::assertStringContainsString('/CenterWindow true', $actual);

        $preferences->reset();
        $actual = $preferences->getOutput();
        self::assertSame('', $actual);
    }

    public function testSetAllToDefault(): void
    {
        $preferences = new PdfViewerPreferences();
        $preferences->setCenterWindow(false);
        $preferences->setDisplayDocTitle(false);
        $preferences->setFitWindow(false);
        $preferences->setHideMenubar(false);
        $preferences->setHideToolbar(false);
        $preferences->setHideWindowUI(false);
        $preferences->setPickTrayByPDFSize(false);

        $preferences->setDuplex(PdfDuplex::NONE);
        $preferences->setDirection(PdfDirection::L2R);
        $preferences->setScaling(PdfScaling::APP_DEFAULT);
        $preferences->setNonFullScreenPageMode(PdfNonFullScreenPageMode::USE_NONE);

        $actual = $preferences->getOutput();
        self::assertSame('', $actual);
    }
}
