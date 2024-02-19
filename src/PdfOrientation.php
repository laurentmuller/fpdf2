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
 * The PDF document orientation enumeration.
 */
enum PdfOrientation
{
    /**
     * Landscape orientation.
     */
    case LANDSCAPE;

    /**
     * Portrait orientation.
     */
    case PORTRAIT;
}
