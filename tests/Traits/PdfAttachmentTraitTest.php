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

namespace fpdf\Tests\Traits;

use fpdf\Enums\PdfDestination;
use fpdf\PdfDocument;
use fpdf\Traits\PdfAttachmentTrait;
use PHPUnit\Framework\TestCase;

class PdfAttachmentTraitTest extends TestCase
{
    public function testAttachment(): void
    {
        $document = new class() extends PdfDocument {
            use PdfAttachmentTrait;
        };
        $document->addPage();
        $document->attach('tests/resources/attachment.txt');
        $document->openAttachmentPane();
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
    }

    public function testNoAttachments(): void
    {
        $document = new class() extends PdfDocument {
            use PdfAttachmentTrait;
        };
        $document->addPage();
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
    }
}
