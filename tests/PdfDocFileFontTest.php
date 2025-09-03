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
use fpdf\PdfDocument;
use fpdf\PdfException;

class PdfDocFileFontTest extends AbstractPdfDocTestCase
{
    private const FONTS_DIR = __DIR__ . '/resources/';

    public function testFontDiff(): void
    {
        $doc = $this->createDocument();
        $doc->addFont(
            family: 'Diff',
            file: 'font_diff.json',
            dir: self::FONTS_DIR
        );
        $doc->output(PdfDestination::STRING);
        self::assertSame(1, $doc->getPage());
    }

    public function testFontInvalidJson(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Unable to parse the font file:.*font_no_name.php.$/');
        $doc = $this->createDocument();
        $doc->addFont(
            family: 'Test',
            file: 'font_no_name.php',
            dir: self::FONTS_DIR
        );
    }

    public function testFontLength2(): void
    {
        $doc = $this->createDocument();
        $doc->addFont(
            family: 'Length2',
            file: 'font_length2.json',
            dir: self::FONTS_DIR
        );
        $doc->output(PdfDestination::STRING);
        self::assertSame(1, $doc->getPage());
    }

    public function testFontNotExist(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/Unable to find the font file:.*fake.json.$/');
        $doc = $this->createDocument();
        $doc->addFont(
            family: 'Test',
            file: 'fake.json',
            dir: self::FONTS_DIR
        );
    }

    public function testNoNameFont(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessageMatches('/No font name defined in file:.*font_no_name.json.$/');
        $doc = $this->createDocument();
        $doc->addFont(
            family: 'Test',
            file: 'font_no_name.json',
            dir: self::FONTS_DIR
        );
    }

    public function testOtherFont(): void
    {
        $doc = $this->createDocument();
        $doc->addFont(
            family: 'Test',
            file: 'font_test.json',
            dir: self::FONTS_DIR
        );
        self::assertSame(1, $doc->getPage());
    }

    public function testOtherType(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Unsupported font type: Test.');
        $doc = $this->createDocument();
        $doc->addFont(
            family: 'Test',
            file: 'font_other.json',
            dir: self::FONTS_DIR
        );
        $doc->output(PdfDestination::STRING);
    }

    public function testPutFontWithCallback(): void
    {
        $doc = new class() extends PdfDocument {
            public function putTest(): void
            {
            }
        };
        $doc->addFont(
            family: 'Test',
            file: 'font_other.json',
            dir: self::FONTS_DIR
        );
        $doc->output(PdfDestination::STRING);
        self::assertSame(1, $doc->getPage());
    }
}
