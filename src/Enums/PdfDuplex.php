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

use Elao\Enum\Attribute\EnumCase;
use fpdf\Interfaces\PdfEnumDefaultInterface;
use fpdf\Traits\PdfEnumDefaultTrait;

/**
 * The PDF duplex enumeration.
 *
 * The paper handling option that shall be used when printing the file from the print dialog.
 *
 * @implements PdfEnumDefaultInterface<PdfDuplex>
 */
enum PdfDuplex: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /** Duplex and flip on the long edge of the sheet. */
    case DUPLEX_FLIP_LONG_EDGE = 'DuplexFlipLongEdge';
    /** Duplex and flip on the short edge of the sheet. */
    case DUPLEX_FLIP_SHORT_EDGE = 'DuplexFlipShortEdge';
    /** No duplex used (default value). */
    #[EnumCase(extras: [self::NAME => true])]
    case NONE = 'None';
    /** Print single-sided. */
    case SIMPLEX = 'Simplex';
}
