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
 * The PDF font name enumeration.
 *
 * @see PdfDocument::addFont()
 * @see PdfDocument::setFont()
 */
enum PdfFontName: string
{
    /**
     * The Arial font name (synonymous: sans serif).
     */
    case ARIAL = 'Arial';

    /**
     * The Courier font name (fixed-width).
     */
    case COURIER = 'Courier';

    /**
     * The Helvetica font name (synonymous: sans serif).
     */
    case HELVETICA = 'Helvetica';

    /**
     * The Symbol font name (symbolic).
     */
    case SYMBOL = 'Symbol';

    /**
     * The Times font name (serif).
     */
    case TIMES = 'Times';

    /**
     * The ZapfDingbats font name (symbolic).
     */
    case ZAPFDINGBATS = 'ZapfDingbats';

    /**
     * Try to find a font name for the given family; ignore case consideration.
     *
     * @param string $family the font family to search font name for
     *
     * @return ?PdfFontName the font name, if found; <code>null</code> otherwise
     */
    public static function tryFromFamily(string $family): ?PdfFontName
    {
        foreach (self::cases() as $fontName) {
            if (0 === \strcasecmp($family, $fontName->value)) {
                return $fontName;
            }
        }

        return null;
    }
}
