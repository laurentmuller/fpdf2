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
 * The PDF document size enumeration.
 */
enum PdfSize: string
{
    /**
     * A3 (297 × 420 mm).
     */
    case A3 = 'A3';

    /**
     * A4 (210 × 297 mm).
     */
    case A4 = 'A4';

    /**
     * A5 (148 × 210 mm).
     */
    case A5 = 'A5';

    /**
     * Legal (8.5 x 14 inches).
     */
    case LEGAL = 'Legal';

    /**
     * Letter (8.5 x 11 inches).
     */
    case LETTER = 'Letter';

    /**
     * Gets the document size.
     *
     * @return array{0: float, 1: float}
     */
    public function getSize(): array
    {
        return match ($this) {
            self::A3 => [841.89, 1190.55],
            self::A4 => [595.28, 841.89],
            self::A5 => [420.94, 595.28],
            self::LEGAL => [612.0, 1008.0],
            self::LETTER => [612.0, 792.0],
        };
    }
}
