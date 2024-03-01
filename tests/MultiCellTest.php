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

#[PHPUnit\Framework\Attributes\CoversClass(FPDF::class)]
#[PHPUnit\Framework\Attributes\CoversClass(PdfDocument::class)]
class MultiCellTest extends AbstractTestCase
{
    private const CONTENT = <<<_TEXT
        First Line with a big text to be sure that a new line is reached.

        Second Line.
        Third Line.
        _TEXT;

    private const TEXT_WIDTH = 80.0;

    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->multiCell(width: self::TEXT_WIDTH, text: 'ALL: ' . self::CONTENT, border: PdfBorder::all());
        $doc->lineBreak();
        $doc->multiCell(text: 'NONE: ' . self::CONTENT, border: PdfBorder::none());
        $doc->lineBreak();
        $doc->multiCell(text: 'LEFT-RIGHT: ' . self::CONTENT, border: PdfBorder::leftRight());
        $doc->lineBreak();
        $doc->multiCell(width: self::TEXT_WIDTH, text: 'TOP-BOTTOM: ' . self::CONTENT, border: PdfBorder::topBottom());
        $doc->lineBreak();
        $doc->multiCell(width: self::TEXT_WIDTH, text: 'BOTTOM: ' . self::CONTENT, border: PdfBorder::bottom());
        $doc->lineBreak();
        $doc->multiCell(width: self::TEXT_WIDTH, text: 'TOP: ' . self::CONTENT, border: PdfBorder::top());
        $doc->lineBreak();
        $doc->multiCell(width: self::TEXT_WIDTH, text: 'LEFT: ' . self::CONTENT, border: PdfBorder::left());
        $doc->lineBreak();
        $doc->multiCell(width: self::TEXT_WIDTH, text: 'RIGHT: ' . self::CONTENT, border: PdfBorder::right());
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->MultiCell(w: self::TEXT_WIDTH, h: 5.0, txt: 'ALL: ' . self::CONTENT, border: 'LRTB');
        $doc->Ln();
        $doc->MultiCell(w: 0.0, h: 5.0, txt: 'NONE: ' . self::CONTENT, border: 0);
        $doc->Ln();
        $doc->MultiCell(w: 0.0, h: 5.0, txt: 'LEFT-RIGHT: ' . self::CONTENT, border: 'LR');
        $doc->Ln();
        $doc->MultiCell(w: self::TEXT_WIDTH, h: 5.0, txt: 'TOP-BOTTOM: ' . self::CONTENT, border: 'TB');
        $doc->Ln();
        $doc->MultiCell(w: self::TEXT_WIDTH, h: 5.0, txt: 'BOTTOM: ' . self::CONTENT, border: 'B');
        $doc->Ln();
        $doc->MultiCell(w: self::TEXT_WIDTH, h: 5.0, txt: 'TOP: ' . self::CONTENT, border: 'T');
        $doc->Ln();
        $doc->MultiCell(w: self::TEXT_WIDTH, h: 5.0, txt: 'LEFT: ' . self::CONTENT, border: 'L');
        $doc->Ln();
        $doc->MultiCell(w: self::TEXT_WIDTH, h: 5.0, txt: 'RIGHT: ' . self::CONTENT, border: 'R');
    }
}
