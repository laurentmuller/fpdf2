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

/**
 * The PDF font style enumeration.
 *
 * @see PdfDocument::addFont()
 * @see PdfDocument::setFont()
 */
enum PdfFontStyle: string
{
    /**
     * Bold.
     *
     * Not allowed for <code>Symbol</code> and <code>ZapfDingbats</code> fonts.
     */
    case BOLD = 'b';

    /**
     * Bold and italic.
     *
     * Not allowed for <code>Symbol</code> and <code>ZapfDingbats</code> fonts.
     */
    case BOLD_ITALIC = 'bi';

    /**
     * Bold, italic and underline.
     *
     * Not allowed for <code>Symbol</code> and <code>ZapfDingbats</code> fonts.
     */
    case BOLD_ITALIC_UNDERLINE = 'biu';

    /**
     * Bold and underline.
     *
     * Not allowed for <code>Symbol</code> and <code>ZapfDingbats</code> fonts.
     */
    case BOLD_UNDERLINE = 'bu';

    /**
     * Italic.
     *
     * Not allowed for <code>Symbol</code> and <code>ZapfDingbats</code> fonts.
     */
    case ITALIC = 'i';

    /**
     * Italic and underline.
     *
     * Not allowed for <code>Symbol</code> and <code>ZapfDingbats</code> fonts.
     */
    case ITALIC_UNDERLINE = 'iu';

    /**
     * Regular (default).
     */
    case REGULAR = '';

    /**
     * Underline.
     */
    case UNDERLINE = 'u';

    /**
     * Returns if this font style contain the underline state.
     */
    public function isUnderLine(): bool
    {
        return \str_contains($this->value, 'u');
    }

    /**
     * Gets the corresponding font style without the underline state.
     */
    public function removeUnderLine(): self
    {
        return match ($this) {
            self::BOLD,
            self::ITALIC,
            self::BOLD_ITALIC,
            self::REGULAR => $this,
            self::UNDERLINE => self::REGULAR,
            self::BOLD_UNDERLINE => self::BOLD,
            self::BOLD_ITALIC_UNDERLINE => self::BOLD_ITALIC,
            self::ITALIC_UNDERLINE => self::ITALIC,
        };
    }
}
