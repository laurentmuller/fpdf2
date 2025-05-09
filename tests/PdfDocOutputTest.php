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
use fpdf\PdfException;

class PdfDocOutputTest extends AbstractPdfDocTestCase
{
    public function testOutputDownloadInvalid(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Some data has already been output, can not send PDF file.');
        $this->expectOutputString('fake');
        $doc = $this->createDocument();
        echo 'fake';
        $doc->output(PdfDestination::DOWNLOAD);
    }

    public function testOutputDownloadValid(): void
    {
        $this->expectOutputRegex('/CreationDate/');
        $doc = $this->createDocument();
        $doc->output(PdfDestination::DOWNLOAD);
        self::assertSame(1, $doc->getPage());
    }

    public function testOutputFileInvalid(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Unable to create output file:.*/');
        $doc = $this->createDocument();
        $doc->output(PdfDestination::FILE, 'https://www.bibi.nu/file.doc');
    }

    public function testOutputInlineInvalid(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Some data has already been output, can not send PDF file.');
        $this->expectOutputString('fake');
        $doc = $this->createDocument();
        echo 'fake';
        $doc->output();
    }

    public function testOutputInlineValid(): void
    {
        $this->expectOutputRegex('/CreationDate/');
        $doc = $this->createDocument();
        $doc->output();
        self::assertSame(1, $doc->getPage());
    }

    public function testOutputStringValid(): void
    {
        $doc = $this->createDocument();
        $value = $doc->output(PdfDestination::STRING);
        self::assertNotEmpty($value);
    }
}
