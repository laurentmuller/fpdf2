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
 * The predominant reading order for text.
 *
 * @implements PdfEnumDefaultInterface<PdfDirection>
 */
enum PdfDirection: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /**
     * Left to right (default value).
     */
    #[EnumCase(extras: [self::NAME => true])]
    case L2R = 'L2R';
    /**
     * Right to left, including vertical writing systems, such as Chinese, Japanese and Korean.
     */
    case R2L = 'R2L';
}
