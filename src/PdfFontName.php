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
 */
enum PdfFontName: string
{
    /**
     * The Arial font name (synonymous: sans serif).
     *
     * This is the default font.
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
}
