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

#[\PHPUnit\Framework\Attributes\CoversClass(PdfDocument::class)]
class PdfDocTextTest extends AbstractPdfDocTestCase
{
    public function testColorFlag(): void
    {
        $doc = $this->createDocument();
        $doc->setFillColor(255, 255, 255);
        $doc->setTextColor(0, 0, 0);
        $doc->text(25, 25, text: 'fake');
        self::assertSame(1, $doc->getPage());
    }

    public function testUnderline(): void
    {
        $doc = $this->createDocument();
        $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::UNDERLINE);
        $doc->text(25, 25, '');
        $doc->text(25, 25, 'fake');
        self::assertSame(1, $doc->getPage());
    }

    public function testWithoutFont(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(true, false);
        $doc->text(25, 25, 'fake');
        self::fail('A PDF exception must be raised.');
    }
}
