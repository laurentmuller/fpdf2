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
 * The PDF document state enumeration.
 *
 * @see PdfDocument::getState()
 */
enum PdfState
{
    /** The document is closed. */
    case CLOSED;
    /** The end page has been called. */
    case END_PAGE;
    /** The document has no page (not started). */
    case NO_PAGE;
    /** The start page has been called. */
    case PAGE_STARTED;
}
