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
 * Font type enumeration.
 */
enum PdfFontType: string
{
    /** The core (embedded) font type. */
    case CORE = 'Core';
    /** The true type font type. */
    case TRUE_TYPE = 'TrueType';
    /** The type 1 font type. */
    case TYPE_1 = 'Type1';
}
