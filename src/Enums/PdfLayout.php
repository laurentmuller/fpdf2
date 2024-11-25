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
 */
enum PdfLayout: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /**
     * This layout is not outputted at all (default value).
     */
    #[EnumCase(extras: [self::NAME => true])]
    case DEFAULT = '';

    /**
     * Displays the pages in one column.
     */
    case ONE_COLUMN = 'OneColumn';

    /**
     * Displays one page at a time.
     */
    case SINGLE_PAGE = 'SinglePage';

    /**
     * Displays the pages in two columns, with odd-numbered pages on the left.
     */
    case TWO_COLUMN_LEFT = 'TwoColumnLeft';

    /**
     * Displays the pages in two columns, with odd-numbered pages on the right.
     */
    case TWO_COLUMN_RIGHT = 'TwoColumnRight';

    /**
     * Displays the pages two at a time, with odd-numbered pages on the left.
     *
     * Require PDF 1.5.
     */
    case TWO_PAGE_LEFT = 'TwoPageLeft';

    /**
     * Displays the pages two at a time, with odd-numbered pages on the right.
     *
     * Require PDF 1.5.
     */
    case TWO_PAGE_RIGHT = 'TwoPageRight';

    /**
     * Gets the required PDF version.
     */
    public function getVersion(): PdfVersion
    {
        return match ($this) {
            self::TWO_PAGE_LEFT,
            self::TWO_PAGE_RIGHT => PdfVersion::VERSION_1_5,
            default => PdfVersion::getDefault()
        };
    }
}
