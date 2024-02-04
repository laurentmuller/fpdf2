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
 * The PDF document zoom enumeration.
 */
enum PdfZoom: string
{
    /**
     * Uses viewer default mode.
     */
    case DEFAULT = 'default';

    /**
     * Displays the entire page on screen.
     */
    case FULL_PAGE = 'fullpage';

    /**
     * Uses maximum width of window.
     */
    case FULL_WIDTH = 'fullwidth';

    /**
     * Uses real size (equivalent to 100% zoom).
     */
    case REAL = 'real';
}
