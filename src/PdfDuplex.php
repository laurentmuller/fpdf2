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

use Elao\Enum\Attribute\EnumCase;

/**
 * The paper handling option that shall be used when printing the file from the print dialog.
 *
 * @implements PdfEnumDefaultInterface<PdfDuplex>
 */
enum PdfDuplex: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /**
     * Duplex and flip on the long edge of the sheet.
     */
    case DUPLEX_FLIP_LONG_EDGE = 'DuplexFlipLongEdge';
    /**
     * Duplex and flip on the short edge of the sheet.
     */
    case DUPLEX_FLIP_SHORT_EDGE = 'DuplexFlipShortEdge';
    /**
     * No duplex used (default value).
     */
    #[EnumCase(extras: [self::NAME => true])]
    case NONE = 'None';
    /**
     * Print single-sided.
     */
    case SIMPLEX = 'Simplex';
}
