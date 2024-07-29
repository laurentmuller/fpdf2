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

namespace fpdf\fixture;

use fpdf\PdfEnumDefaultInterface;
use fpdf\PdfEnumDefaultTrait;

/**
 * @implements PdfEnumDefaultInterface<PdfInvalidEnumDefault>
 */
enum PdfInvalidEnumDefault implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    case FIRST;
    case SECOND;
}
