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

    /** 1999, PDF 1.3 / Acrobat 4 (default value). */
    #[EnumCase(extras: [self::NAME => true])]
    case VERSION_1_3 = '1.3';
    /** 2001, PDF 1.4 / Acrobat 5. */
    case VERSION_1_4 = '1.4';
    /** 2003, PDF 1.5 / Acrobat 6. */
    case VERSION_1_5 = '1.5';
    /** 2005, PDF 1.6 / Acrobat 7. */
    case VERSION_1_6 = '1.6';
    /** 2006, PDF 1.7 / Acrobat 8. */
    case VERSION_1_7 = '1.7';

    /**
     * Gets this value, as string, when output to the document.
     */
    public function getOutput(): string
    {
        return \sprintf('%%PDF-%s', $this->value);
    }

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

    /**
     * Gets the highest version.
     *
     * @param PdfVersion $a the first version to compare
     * @param PdfVersion $b the second version to compare
     */
    public static function max(PdfVersion $a, PdfVersion $b): PdfVersion
    {
        return $a->isSmaller($b) ? $b : $a;
    }
}
