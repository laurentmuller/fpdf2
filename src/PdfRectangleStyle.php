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
 * The PDF style to draw and/or fill rectangle.
 *
 * @see PdfDocument::rect()
 */
enum PdfRectangleStyle: string
{
    /**
     * Draw the border around the rectangle.
     */
    case BORDER = 'S';

    /**
     * Draw the border and fill the rectangle.
     */
    case BOTH = 'B';

    /**
     * Fill the rectangle.
     */
    case FILL = 'f';
}
