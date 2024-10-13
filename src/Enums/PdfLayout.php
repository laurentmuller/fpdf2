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
 * The PDF display layout enumeration.
 *
 * @implements PdfEnumDefaultInterface<PdfLayout>
 *
 * @see PdfDocument::setDisplayMode()
 */
enum PdfLayout: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /**
     * Displays pages continuously.
     */
    case CONTINUOUS = 'OneColumn';

    /**
     * Uses layout default mode.
     */
    #[EnumCase(extras: [self::NAME => true])]
    case DEFAULT = '';

    /**
     * Displays one page at once.
     */
    case SINGLE = 'SinglePage';

    /**
     * Displays two pages on two columns.
     */
    case TWO_PAGES = 'TwoColumnLeft';
}
