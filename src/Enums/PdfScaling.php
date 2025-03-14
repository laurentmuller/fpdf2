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
 * The PDF scaling enumeration.
 *
 * The page scaling option that shall be selected when a print dialog is displayed for this document.
 *
 * @implements PdfEnumDefaultInterface<PdfScaling>
 */
enum PdfScaling: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /**
     * Indicates the conforming reader’s default print scaling (default value).
     */
    #[EnumCase(extras: [self::NAME => true])]
    case APP_DEFAULT = 'AppDefault';
    /**
     * Indicates no page scaling.
     */
    case NONE = 'None';
}
