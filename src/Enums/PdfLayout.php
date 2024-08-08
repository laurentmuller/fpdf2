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

namespace fpdf\Enums;

/**
 * The PDF display layout enumeration.
 *
 * @see PdfDocument::setDisplayMode()
 */
enum PdfLayout
{
    /**
     * Displays pages continuously.
     */
    case CONTINUOUS;

    /**
     * Uses layout default mode.
     */
    case DEFAULT;

    /**
     * Displays one page at once.
     */
    case SINGLE;

    /**
     * Displays two pages on two columns.
     */
    case TWO_PAGES;
}
