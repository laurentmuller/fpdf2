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

namespace fpdf\Tests\fixture;

use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfTextAlignment;
use fpdf\PdfBorder;
use fpdf\PdfDocument;

/**
 * Override the multi-cell function for testing purpose.
 */
class PdfMultiCellLines extends PdfDocument
{
    #[\Override]
    public function multiCell(
        ?float $width = null,
        float $height = self::LINE_HEIGHT,
        string $text = '',
        ?PdfBorder $border = null,
        PdfTextAlignment $align = PdfTextAlignment::JUSTIFIED,
        bool $fill = false
    ): static {
        $lines = $this->splitText($text, $width);
        if ([] === $lines) {
            return $this;
        }

        $border ??= PdfBorder::none();
        if ($border->isAll()) {
            $border1 = PdfBorder::all()->setBottom(false);
            $border2 = PdfBorder::leftRight();
        } else {
            $border1 = new PdfBorder($border->isLeft(), $border->isTop(), $border->isRight(), false);
            $border2 = new PdfBorder($border->isLeft(), false, $border->isRight(), false);
        }

        $firstKey = \array_key_first($lines);
        $lastKey = \array_key_last($lines);
        $width ??= $this->getRemainingWidth();
        $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;

        foreach ($lines as $key => $line) {
            // last line?
            if ($lastKey === $key) {
                if ($border->isBottom()) {
                    $border1->setBottom();
                }
                if ($this->wordSpacing > 0) {
                    $this->updateWordSpacing();
                }
            } elseif (PdfTextAlignment::JUSTIFIED === $align) {
                $separators = \substr_count($line, ' ');
                if ($separators > 0) {
                    $textWidth = $this->getStringWidth($line) * 1000.0 / $this->fontSize;
                    $wordSpacing = ($widthMax - $textWidth) / 1000.0 * $this->fontSize / (float) $separators;
                    if ($wordSpacing > 0.0) {
                        $this->updateWordSpacing($wordSpacing);
                    }
                }
            }

            $this->cell(
                $width,
                $height,
                $line,
                $border1,
                PdfMove::BELOW,
                $align,
                $fill
            );

            // first line?
            if ($firstKey === $key) {
                $border1 = clone $border2;
            }
        }

        return $this;
    }
}
