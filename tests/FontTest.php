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

class FontTest extends AbstractTestCase
{
    private const FONT_NAME = 'Roboto-Light';
    private const FONTS_DIR = __DIR__ . '/fonts/';

    /**
     * @throws PdfException
     */
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->addFont(self::FONT_NAME, PdfFontStyle::REGULAR, self::FONT_NAME . '.php', self::FONTS_DIR);
        $doc->setFont(self::FONT_NAME, size: 9.0);

        foreach (\range(10.0, 24.0, 2.0) as $size) {
            $doc->setFontSize($size);
            $doc->cell(
                0.0,
                $size,
                \sprintf('Font name "%s" and size: "%.0f".', self::FONT_NAME, $size),
                move: PdfMove::BELOW
            );
        }
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->AddFont(self::FONT_NAME, '', self::FONT_NAME . '.php', self::FONTS_DIR);
        $doc->SetFont(self::FONT_NAME, size: 9.0);

        foreach (\range(10.0, 24.0, 2.0) as $size) {
            $doc->SetFontSize($size);
            $doc->Cell(
                0.0,
                $size,
                \sprintf('Font name "%s" and size: "%.0f".', self::FONT_NAME, $size),
                ln: 1
            );
        }
    }
}
