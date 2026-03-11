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
use fpdf\Tests\Fixture\PdfDocumentEllipse;
use PHPUnit\Framework\TestCase;

final class PdfEllipseTraitTest extends TestCase
{
    public function testRender(): void
    {
        $document = new PdfDocumentEllipse();
        $document->addPage();
        $document->circle(100, 100, 25);
        $document->ellipse(100, 100, 25, 50);
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
    }
}
