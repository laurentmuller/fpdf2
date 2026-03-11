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
 * The PDF document output enumeration.
 *
 * @see PdfDocument::output()
 */
enum PdfDestination
{
    /** Send to the browser and force a file download with the given name parameter. */
    case DOWNLOAD;

    /** Save to a local file with the given name parameter (may include a path). */
    case FILE;

    /**
     * Send the file inline to the browser.
     *
     * The PDF viewer is used if available.
     */
    case INLINE;

    /** Return the document as a string. */
    case STRING;
}
