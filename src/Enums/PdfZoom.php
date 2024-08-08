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
 * The PDF document zoom enumeration.
 *
 * @see PdfDocument::setDisplayMode()
 */
enum PdfZoom
{
    /**
     * Uses viewer default mode.
     */
    case DEFAULT;

    /**
     * Displays the entire page on screen.
     */
    case FULL_PAGE;

    /**
     * Uses maximum width of the window.
     */
    case FULL_WIDTH;

    /**
     * Uses real size (equivalent to 100% zoom).
     */
    case REAL;
}
