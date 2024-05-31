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
 * The page scaling option that shall be selected when a print dialog is displayed for this document.
 *
 * @implements PdfEnumDefaultInterface<PdfScaling>
 */
enum PdfScaling: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /**
     * Indicates the conforming reader’s default print scaling.
     */
    #[EnumCase(extras: [self::NAME => true])]
    case APP_DEFAULT = 'AppDefault';
    /**
     * Indicates no page scaling.
     */
    case NONE = 'None';
}
