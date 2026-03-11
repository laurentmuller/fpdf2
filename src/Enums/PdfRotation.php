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
 * The PDF document rotation enumeration.
 *
 * @see PdfDocument::addPage()
 */
enum PdfRotation: int
{
    /** 180 degree clockwise. */
    case CLOCKWISE_180 = 180;
    /** 270 degree clockwise. */
    case CLOCKWISE_270 = 270;
    /** 90 degree clockwise. */
    case CLOCKWISE_90 = 90;
    /** 0 degree clockwise. */
    case DEFAULT = 0;
}
