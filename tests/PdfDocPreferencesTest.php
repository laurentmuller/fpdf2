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
use fpdf\Enums\PdfDirection;

class PdfDocPreferencesTest extends AbstractPdfDocTestCase
{
    public function testPreferences(): void
    {
        $doc = $this->createDocument();
        $preferences = $doc->getViewerPreferences();
        $actual = $preferences->getDirection();
        self::assertSame(PdfDirection::L2R, $actual);

        $preferences->setPickTrayByPDFSize();
        $actual = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('%PDF-1.7', $actual);
        self::assertStringContainsString('/ViewerPreferences<<', $actual);
        self::assertStringContainsString('/PickTrayByPDFSize true', $actual);
    }
}
