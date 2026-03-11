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
 * PDF move enumeration.
 *
 * Indicates where the current position should go after a cell is printed.
 *
 * @see PdfDocument::cell()
 */
enum PdfMove
{
    /** Move below of the printed cell. */
    case BELOW;

    /**
     * Move at the beginning of the next line after the cell is printed.
     *
     * It is equivalent to the setting <code>PdfMove::RIGHT</code> and calling the
     * <code>PdfDocument::lineBreak()</code> method immediately afterward.
     */
    case NEW_LINE;

    /** Move to the right position of the printed cell. */
    case RIGHT;
}
