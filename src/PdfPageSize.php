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
use Elao\Enum\ExtrasTrait;

/**
 * The PDF document page size enumeration.
 */
enum PdfPageSize: string
{
    use ExtrasTrait;

    /** A0 (841 x 1189 mm). */
    #[EnumCase(extras: ['width' => 841, 'height' => 1189, 'unit' => 'mm'])]
    case A0 = 'A0';
    /** A1 (594 x 841 mm). */
    #[EnumCase(extras: ['width' => 594, 'height' => 841, 'unit' => 'mm'])]
    case A1 = 'A1';
    /** A10 (26 x 37 mm). */
    #[EnumCase(extras: ['width' => 26, 'height' => 37, 'unit' => 'mm'])]
    case A10 = 'A10';
    /** A2 (420 x 594 mm). */
    #[EnumCase(extras: ['width' => 420, 'height' => 594, 'unit' => 'mm'])]
    case A2 = 'A2';
    /** A3 (297 x 420 mm). */
    #[EnumCase(extras: ['width' => 297, 'height' => 420, 'unit' => 'mm'])]
    case A3 = 'A3';
    /** A4 (210 x 297 mm). */
    #[EnumCase(extras: ['width' => 210, 'height' => 297, 'unit' => 'mm'])]
    case A4 = 'A4';
    /** A5 (148 x 210 mm). */
    #[EnumCase(extras: ['width' => 148, 'height' => 210, 'unit' => 'mm'])]
    case A5 = 'A5';
    /** A6 (105 x 148 mm). */
    #[EnumCase(extras: ['width' => 105, 'height' => 148, 'unit' => 'mm'])]
    case A6 = 'A6';
    /** A7 (74 x 105 mm). */
    #[EnumCase(extras: ['width' => 74, 'height' => 105, 'unit' => 'mm'])]
    case A7 = 'A7';
    /** A8 (52 x 74 mm). */
    #[EnumCase(extras: ['width' => 52, 'height' => 74, 'unit' => 'mm'])]
    case A8 = 'A8';
    /** A9 (37 x 52 mm). */
    #[EnumCase(extras: ['width' => 37, 'height' => 52, 'unit' => 'mm'])]
    case A9 = 'A9';

    /** ANSI C (17 x 22 inch). */
    #[EnumCase(extras: ['width' => 17, 'height' => 22, 'unit' => 'in'])]
    case ANSI_C = 'ANSI_C';
    /** ANSI D (22 x 34 inch). */
    #[EnumCase(extras: ['width' => 22, 'height' => 34, 'unit' => 'in'])]
    case ANSI_D = 'ANSI_D';
    /** ANSI E (34 x 44 inch). */
    #[EnumCase(extras: ['width' => 34, 'height' => 44, 'unit' => 'in'])]
    case ANSI_E = 'ANSI_E';

    /** B0 (1000 x 1414 mm). */
    #[EnumCase(extras: ['width' => 1000, 'height' => 1414, 'unit' => 'mm'])]
    case B0 = 'B0';
    /** B1 (707 x 1000 mm). */
    #[EnumCase(extras: ['width' => 707, 'height' => 1000, 'unit' => 'mm'])]
    case B1 = 'B1';
    /** B10 (31 x 44 mm). */
    #[EnumCase(extras: ['width' => 31, 'height' => 44, 'unit' => 'mm'])]
    case B10 = 'B10';
    /** B2 (500 x 707 mm). */
    #[EnumCase(extras: ['width' => 500, 'height' => 707, 'unit' => 'mm'])]
    case B2 = 'B2';
    /** B3 (353 x 500 mm). */
    #[EnumCase(extras: ['width' => 353, 'height' => 500, 'unit' => 'mm'])]
    case B3 = 'B3';
    /** B4 (250 x 353 mm). */
    #[EnumCase(extras: ['width' => 250, 'height' => 353, 'unit' => 'mm'])]
    case B4 = 'B4';
    /** B5 (176 x 250 mm). */
    #[EnumCase(extras: ['width' => 176, 'height' => 250, 'unit' => 'mm'])]
    case B5 = 'B5';
    /** B6 (125 x 176 mm). */
    #[EnumCase(extras: ['width' => 125, 'height' => 176, 'unit' => 'mm'])]
    case B6 = 'B6';
    /** B7 (88 x 125 mm). */
    #[EnumCase(extras: ['width' => 88, 'height' => 125, 'unit' => 'mm'])]
    case B7 = 'B7';
    /** B8 (62 x 88 mm). */
    #[EnumCase(extras: ['width' => 62, 'height' => 88, 'unit' => 'mm'])]
    case B8 = 'B8';
    /** B9 (44 x 62 mm). */
    #[EnumCase(extras: ['width' => 44, 'height' => 62, 'unit' => 'mm'])]
    case B9 = 'B9';

    /** C0 (917 x 1297 mm). */
    #[EnumCase(extras: ['width' => 917, 'height' => 1297, 'unit' => 'mm'])]
    case C0 = 'C0';
    /**C1 (648 x 917 mm). */
    #[EnumCase(extras: ['width' => 648, 'height' => 917, 'unit' => 'mm'])]
    case C1 = 'C1';
    /** C10 (38 x 40 mm). */
    #[EnumCase(extras: ['width' => 38, 'height' => 40, 'unit' => 'mm'])]
    case C10 = 'C10';
    /** C2 (458 x 648 mm). */
    #[EnumCase(extras: ['width' => 458, 'height' => 648, 'unit' => 'mm'])]
    case C2 = 'C2';
    /** C3 (324 x 458 mm). */
    #[EnumCase(extras: ['width' => 324, 'height' => 458, 'unit' => 'mm'])]
    case C3 = 'C3';
    /** C4 (229 x 324 mm). */
    #[EnumCase(extras: ['width' => 229, 'height' => 324, 'unit' => 'mm'])]
    case C4 = 'C4';
    /** C5 (162 x 229 mm). */
    #[EnumCase(extras: ['width' => 162, 'height' => 229, 'unit' => 'mm'])]
    case C5 = 'C5';
    /** C6 (114 x 162 mm). */
    #[EnumCase(extras: ['width' => 114, 'height' => 162, 'unit' => 'mm'])]
    case C6 = 'C6';
    /** C7 (81 x 114 mm). */
    #[EnumCase(extras: ['width' => 81, 'height' => 114, 'unit' => 'mm'])]
    case C7 = 'C7';
    /** C8 (57 x 81 mm). */
    #[EnumCase(extras: ['width' => 57, 'height' => 81, 'unit' => 'mm'])]
    case C8 = 'C8';
    /** C9 (40 x 57 mm). */
    #[EnumCase(extras: ['width' => 40, 'height' => 57, 'unit' => 'mm'])]
    case C9 = 'C9';

    /** EXECUTIVE (7.5 x 10 inch). */
    #[EnumCase(extras: ['width' => 7.5, 'height' => 10, 'unit' => 'in'])]
    case EXECUTIVE = 'EXECUTIVE';
    /** LEDGER (11 x 17 inch). */
    #[EnumCase(extras: ['width' => 11, 'height' => 17, 'unit' => 'in'])]
    case LEDGER = 'LEDGER';
    /** LEGAL (8.5 x 14 inch). */
    #[EnumCase(extras: ['width' => 8.5, 'height' => 14, 'unit' => 'in'])]
    case LEGAL = 'LEGAL';
    /** LETTER (8.5 x 11 inch). */
    #[EnumCase(extras: ['width' => 8.5, 'height' => 11, 'unit' => 'in'])]
    case LETTER = 'LETTER';

    /**
     * Gets the page height.
     */
    public function getHeight(): float
    {
        /* @phpstan-ignore-next-line */
        return (float) $this->getExtra('height', true);
    }

    /**
     * Gets the document size in point.
     */
    public function getSize(): PdfSize
    {
        $scaleFactor = $this->getUnit()->getScaleFactor();
        $width = \round($this->getWidth() * $scaleFactor, 2);
        $height = \round($this->getHeight() * $scaleFactor, 2);

        return PdfSize::instance($width, $height);
    }

    /**
     * Gets the document unit.
     */
    public function getUnit(): PdfUnit
    {
        /** @psalm-var string $unit */
        $unit = $this->getExtra('unit', true);

        return PdfUnit::from($unit);
    }

    /**
     * Gets the page width.
     */
    public function getWidth(): float
    {
        /* @phpstan-ignore-next-line */
        return (float) $this->getExtra('width', true);
    }
}
