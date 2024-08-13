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
use fpdf\PdfDocument;
use fpdf\Traits\PdfEnumDefaultTrait;

/**
 * PDF version enumeration.
 *
 * @implements PdfEnumDefaultInterface<PdfVersion>
 *
 * @see PdfDocument::updatePdfVersion()
 */
enum PdfVersion: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /**
     * 1999, PDF 1.3 / Acrobat 4. This is the default version.
     */
    #[EnumCase(extras: [self::NAME => true])]
    case VERSION_1_3 = '1.3';
    /**
     * 2001, PDF 1.4 / Acrobat 5.
     */
    case VERSION_1_4 = '1.4';
    /**
     * 2003, PDF 1.5 / Acrobat 6.
     */
    case VERSION_1_5 = '1.5';
    /**
     * 2005, PDF 1.6 / Acrobat 7.
     */
    case VERSION_1_6 = '1.6';
    /**
     * 2006, PDF 1.7 / Acrobat 8.
     */
    case VERSION_1_7 = '1.7';

    /**
     * Returns a value indicating if this version is smaller than the given version.
     *
     * @param PdfVersion $other the version to compare to
     *
     * @return bool <code>true</code> if this version is smaller; <code>false</code> if equal or greater
     */
    public function isSmaller(PdfVersion $other): bool
    {
        return \version_compare($this->value, $other->value, '<');
    }
}
