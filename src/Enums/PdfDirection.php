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
 * The PDF direction enumeration.
 *
 * The predominant reading order for text.
 *
 * @implements PdfEnumDefaultInterface<PdfDirection>
 */
enum PdfDirection: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /** Left to right (default value). */
    #[EnumCase(extras: [self::NAME => true])]
    case L2R = 'L2R';
    /** Right to left, including vertical writing systems, such as Chinese, Japanese and Korean. */
    case R2L = 'R2L';
}
