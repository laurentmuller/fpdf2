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

namespace fpdf;

use fpdf\Enums\PdfDestination;

class PdfDocOutputTest extends AbstractPdfDocTestCase
{
    public function testOutputDownloadInvalid(): void
    {
        $this->expectOutputString('fake');
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        echo 'fake';
        $doc->output(PdfDestination::DOWNLOAD);
        self::fail('A PDF exception must be raised.');
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
        $doc = $this->createDocument();
        $doc->output(PdfDestination::FILE, 'https://www.bibi.nu/file.doc');
        self::fail('A PDF exception must be raised.');
    }

    public function testOutputInlineInvalid(): void
    {
        $this->expectOutputString('fake');
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        echo 'fake';
        $doc->output();
        self::fail('A PDF exception must be raised.');
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
