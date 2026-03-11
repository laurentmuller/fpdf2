<?php

/*
 * This file is part of the FPDF2 package.
 *
 * (c) bibi.nu <bibi@bibi.nu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace fpdf\Tests\Traits;

use fpdf\Enums\PdfDestination;
use fpdf\Tests\Fixture\PdfDocumentTransparency;
use PHPUnit\Framework\TestCase;

final class PdfTransparencyTraitTest extends TestCase
{
    public function testEmptyTransparency(): void
    {
        $document = new PdfDocumentTransparency();
        $document->addPage();
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
    }

    public function testRender(): void
    {
        $document = new PdfDocumentTransparency();
        $document->addPage();
        $document->setAlpha(0.5);
        $document->resetAlpha();
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
    }
}
