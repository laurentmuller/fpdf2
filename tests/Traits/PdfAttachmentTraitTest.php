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
use fpdf\Enums\PdfVersion;
use fpdf\PdfException;
use fpdf\Tests\fixture\PdfDocumentAttachment;
use PHPUnit\Framework\TestCase;

class PdfAttachmentTraitTest extends TestCase
{
    public function testAttachmentWithDescription(): void
    {
        $file = __DIR__ . '/../resources/attachment.txt';

        $document = new PdfDocumentAttachment();
        $document->addPage();
        $document->addAttachment(file: $file, description: 'Attached File.');
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
        self::assertSame(PdfVersion::VERSION_1_6, $document->getPdfVersion());
    }

    public function testAttachmentWithoutDescription(): void
    {
        $file = __DIR__ . '/../resources/attachment.txt';

        $document = new PdfDocumentAttachment();
        $document->addPage();
        $document->addAttachment(file: $file);
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
        self::assertSame(PdfVersion::VERSION_1_4, $document->getPdfVersion());
    }

    public function testInvalidFile(): void
    {
        $file = __DIR__ . '/fake.txt';

        self::expectException(PdfException::class);
        $document = new PdfDocumentAttachment();
        $document->addPage();
        $document->addAttachment(file: $file);
        $document->output(PdfDestination::STRING);
    }

    public function testNoAttachment(): void
    {
        $document = new PdfDocumentAttachment();
        $document->addPage();
        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
        self::assertSame(PdfVersion::VERSION_1_3, $document->getPdfVersion());
    }
}
