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

use Elao\Enum\Attribute\EnumCase;
use fpdf\Interfaces\PdfEnumDefaultInterface;
use fpdf\Traits\PdfEnumDefaultTrait;

/**
 * The PDF document zoom enumeration.
 *
 * @implements PdfEnumDefaultInterface<PdfZoom>
 */
enum PdfZoom: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /** Uses viewer default mode (default value). */
    #[EnumCase(extras: [self::NAME => true])]
    case DEFAULT = '';

    /** Displays the entire page on screen. */
    case FULL_PAGE = 'Fit';

    /** Uses maximum width of the window. */
    case FULL_WIDTH = 'FitH null';

    /** Uses real size. It is equivalent to 100% zoom. */
    case REAL = 'XYZ null null 1';
}
