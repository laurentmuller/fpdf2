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
 * PDF move enumeration.
 */
enum PdfMove
{
    /**
     * Move below of the printed cell.
     */
    case BELOW;

    /**
     * Move at the beginning of the next line after the cell is printed.
     *
     * It is equivalent to the setting <b>RIGHT</b> and calling the
     * <code>PdfDocument->Ln()</code> method immediately afterward.
     */
    case NEW_LINE;

    /**
     * Move to the right position of the printed cell.
     */
    case RIGHT;
}
