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

class PdfDocWriteTest extends AbstractPdfDocTestCase
{
    public function testMultiLine(): void
    {
        $doc = $this->createDocument();
        $text = \str_repeat('This is a very long text to use for multi lines. ', 20);
        $doc->write(5.0, $text);
        self::assertSame(1, $doc->getPage());
    }

    public function testMultiLineNoSep(): void
    {
        $doc = $this->createDocument();
        $text = \str_repeat('Thisisaverylongtexttouseformultilines', 20);
        $doc->write(5.0, $text);
        self::assertSame(1, $doc->getPage());
    }

    public function testUnderline(): void
    {
        $doc = $this->createDocument();
        $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::UNDERLINE);
        $doc->write(5.0, '');
        $doc->write(5.0, 'fake');
        self::assertSame(1, $doc->getPage());
    }

    public function testWithoutFont(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(true, false);
        $doc->write(5.0, 'fake');
        self::fail('A PDF exception must be raised.');
    }
}
