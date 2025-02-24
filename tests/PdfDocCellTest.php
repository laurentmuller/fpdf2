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

use fpdf\Color\PdfRgbColor;
use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfTextAlignment;
use fpdf\PdfException;

class PdfDocCellTest extends AbstractPdfDocTestCase
{
    public function testColorFlag(): void
    {
        $doc = $this->createDocument();
        $doc->setFillColor(PdfRgbColor::white());
        $doc->setTextColor(PdfRgbColor::black());
        $doc->cell(text: 'fake', align: PdfTextAlignment::JUSTIFIED);
        self::assertSame(1, $doc->getPage());
    }

    public function testJustify(): void
    {
        $doc = $this->createDocument();
        $text = 'This is a very long text to use for multi lines.';
        for ($i = 0; $i < 100; ++$i) {
            $doc->cell(text: $text, move: PdfMove::BELOW, align: PdfTextAlignment::JUSTIFIED);
        }
        self::assertSame(2, $doc->getPage());
    }

    public function testUnderline(): void
    {
        $doc = $this->createDocument();
        $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::UNDERLINE);
        $doc->cell();
        $doc->cell(text: 'fake', align: PdfTextAlignment::JUSTIFIED);
        self::assertSame(1, $doc->getPage());
    }

    public function testWithoutFont(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument(true, false);
        $doc->cell(text: 'fake');
    }
}
