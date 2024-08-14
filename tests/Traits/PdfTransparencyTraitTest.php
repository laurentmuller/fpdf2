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

namespace fpdf\Traits;

use fpdf\Enums\PdfDestination;
use fpdf\PdfDocument;
use PHPUnit\Framework\TestCase;

class PdfTransparencyTraitTest extends TestCase
{
    public function testRender(): void
    {
        $document = new class() extends PdfDocument {
            use PdfTransparencyTrait;
        };
        $document->addPage();
        $document->setAlpha(0.5);
        $document->resetAlpha();
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
    }
}