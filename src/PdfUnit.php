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

/**
 * The PDF document unit enumeration.
 */
enum PdfUnit: string
{
    /**
     * Centimeter.
     */
    case CENTIMETER = 'cm';

    /**
     * Inch.
     */
    case INCH = 'in';

    /**
     * Millimeter.
     */
    case MILLIMETER = 'mm';

    /**
     * Point.
     */
    case POINT = 'pt';

    /**
     * Get the document scale factor.
     */
    public function getScaleFactor(): float
    {
        return match ($this) {
            self::CENTIMETER => 72.0 / 2.54,
            self::INCH => 72.0,
            self::MILLIMETER => 72.0 / 25.4,
            self::POINT => 1.0,
        };
    }
}
