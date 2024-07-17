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

class PdfDocMultiCellTest extends AbstractPdfDocTestCase
{
    public function testCell(): void
    {
        $doc = $this->createDocument();
        $text = \str_repeat('This is a very long text to use for multi lines. ', 10);
        $doc->cell(text: $text);
        self::assertSame(1, $doc->getPage());
    }

    public function testCellNoFont(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $text = \str_repeat('This is a very long text to use for multi lines. ', 10);
        $doc->cell(text: $text);
        self::fail('A PDF exception must be raised.');
    }

    public function testCellNoSep(): void
    {
        $doc = $this->createDocument();
        $text = \str_repeat('isisaverylongtexttouseformultilines', 20);
        $doc->cell(text: $text);
        self::assertSame(1, $doc->getPage());
    }

    public function testJustify(): void
    {
        $doc = $this->createDocument();
        $text = \str_repeat('This is a very long text to use for multi lines.', 5);
        for ($i = 0; $i < 100; ++$i) {
            $doc->multiCell(text: $text);
        }
        self::assertSame(4, $doc->getPage());
    }

    public function testLineCount(): void
    {
        $doc = $this->createDocument();
        $text = \str_repeat('This is a very long text to use for multi lines. ', 10);
        $actual = $doc->getLinesCount($text);
        self::assertSame(4, $actual);
    }

    public function testLineCountNoFont(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $text = \str_repeat('This is a very long text to use for multi lines. ', 10);
        $doc->getLinesCount($text);
        self::fail('A PDF exception must be raised.');
    }

    public function testLineCountNoSep(): void
    {
        $doc = $this->createDocument();
        $text = \str_repeat('Thisisaverylongtexttouseformultilines', 12);
        $actual = $doc->getLinesCount($text);
        self::assertSame(4, $actual);
    }

    public function testMultiCell(): void
    {
        $doc = $this->createDocument();
        $text = \str_repeat('This is a very long text to use for multi lines. ', 10);
        $doc->multiCell(text: $text);
        self::assertSame(1, $doc->getPage());
    }

    public function testMultiCellNoFont(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(false, false);
        $text = \str_repeat('This is a very long text to use for multi lines. ', 10);
        $doc->multiCell(text: $text);
        self::fail('A PDF exception must be raised.');
    }

    public function testMultiCellNoSep(): void
    {
        $doc = $this->createDocument();
        $text = \str_repeat('Thisisaverylongtexttouseformultilines', 20);
        $doc->multiCell(text: $text);
        self::assertSame(1, $doc->getPage());
    }
}
