<?php

/*
 * This file is part of the FPDF2 package.
 *
 * (c) bibi.nu <bibi@bibi.nu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace fpdf\Enums;

/**
 * The PDF cell text alignment enumeration.
 *
 * @see PdfDocument::cell()
 * @see PdfDocument::multiCell()
 */
enum PdfTextAlignment
{
    /** Center alignment. */
    case CENTER;

    /** Justified alignment (only valid when output multi-cell). */
    case JUSTIFIED;

    /** Left alignment. */
    case LEFT;

    /** Right alignment. */
    case RIGHT;
}
