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
 * The PDF document page size enumeration.
 */
enum PdfPageSize: string
{
    /** A0 (841 x 1189 mm). */
    case A0 = 'A0';
    /** A1 (594 x 841 mm). */
    case A1 = 'A1';
    /** A10 (26 x 37 mm). */
    case A10 = 'A10';
    /** A2 (420 x 594 mm). */
    case A2 = 'A2';
    /** A3 (297 x 420 mm). */
    case A3 = 'A3';
    /** A4 (210 x 297 mm). */
    case A4 = 'A4';
    /** A5 (148 x 210 mm). */
    case A5 = 'A5';
    /** A6 (105 x 148 mm). */
    case A6 = 'A6';
    /** A7 (74 x 105 mm). */
    case A7 = 'A7';
    /** A8 (52 x 74 mm). */
    case A8 = 'A8';
    /** A9 (37 x 52 mm). */
    case A9 = 'A9';
    /** ANSI C (17 x 22 inch). */
    case ANSI_C = 'ANSI_C';
    /** ANSI D (22 x 34 inch). */
    case ANSI_D = 'ANSI_D';
    /** ANSI E (34 x 44 inch). */
    case ANSI_E = 'ANSI_E';
    /** B0 (1000 x 1414 mm). */
    case B0 = 'B0';
    /** B1 (707 x 1000 mm). */
    case B1 = 'B1';
    /** B10 (31 x 44 mm). */
    case B10 = 'B10';
    /** B2 (500 x 707 mm). */
    case B2 = 'B2';
    /** B3 (353 x 500 mm). */
    case B3 = 'B3';
    /** B4 (250 x 353 mm). */
    case B4 = 'B4';
    /** B5 (176 x 250 mm). */
    case B5 = 'B5';
    /** B6 (125 x 176 mm). */
    case B6 = 'B6';
    /** B7 (88 x 125 mm). */
    case B7 = 'B7';
    /** B8 (62 x 88 mm). */
    case B8 = 'B8';
    /** B9 (44 x 62 mm). */
    case B9 = 'B9';
    /** C0 (917 x 1297 mm). */
    case C0 = 'C0';
    /**C1 (648 x 917 mm). */
    case C1 = 'C1';
    /** C10 (38 x 40 mm). */
    case C10 = 'C10';
    /** C2 (458 x 648 mm). */
    case C2 = 'C2';
    /** C3 (324 x 458 mm). */
    case C3 = 'C3';
    /** C4 (229 x 324 mm). */
    case C4 = 'C4';
    /** C5 (162 x 229 mm). */
    case C5 = 'C5';
    /** C6 (114 x 162 mm). */
    case C6 = 'C6';
    /** C7 (81 x 114 mm). */
    case C7 = 'C7';
    /** C8 (57 x 81 mm). */
    case C8 = 'C8';
    /** C9 (40 x 57 mm). */
    case C9 = 'C9';
    /** EXECUTIVE (7.5 x 10 inch). */
    case EXECUTIVE = 'EXECUTIVE';
    /** LEDGER (11 x 17 inch). */
    case LEDGER = 'LEDGER';
    /** LEGAL (8.5 x 14 inch). */
    case LEGAL = 'LEGAL';
    /** LETTER (8.5 x 11 inch). */
    case LETTER = 'LETTER';

    /**
     * Gets the document size in point.
     */
    public function getSize(): PdfSize
    {
        return match ($this) {
            self::A0 => $this->computeSize(841, 1189),
            self::A1 => $this->computeSize(594, 841),
            self::A2 => $this->computeSize(420, 594),
            self::A3 => $this->computeSize(297, 420),
            self::A4 => $this->computeSize(210, 297),
            self::A5 => $this->computeSize(148, 210),
            self::A6 => $this->computeSize(105, 148),
            self::A7 => $this->computeSize(74, 105),
            self::A8 => $this->computeSize(52, 74),
            self::A9 => $this->computeSize(37, 52),
            self::A10 => $this->computeSize(26, 37),

            self::B0 => $this->computeSize(1000, 1414),
            self::B1 => $this->computeSize(707, 1000),
            self::B2 => $this->computeSize(500, 707),
            self::B3 => $this->computeSize(353, 500),
            self::B4 => $this->computeSize(250, 353),
            self::B5 => $this->computeSize(176, 250),
            self::B6 => $this->computeSize(125, 176),
            self::B7 => $this->computeSize(88, 125),
            self::B8 => $this->computeSize(62, 88),
            self::B9 => $this->computeSize(44, 62),
            self::B10 => $this->computeSize(31, 44),

            self::C0 => $this->computeSize(917, 1297),
            self::C1 => $this->computeSize(648, 917),
            self::C2 => $this->computeSize(458, 648),
            self::C3 => $this->computeSize(324, 458),
            self::C4 => $this->computeSize(229, 324),
            self::C5 => $this->computeSize(162, 229),
            self::C6 => $this->computeSize(114, 162),
            self::C7 => $this->computeSize(81, 114),
            self::C8 => $this->computeSize(57, 81),
            self::C9 => $this->computeSize(40, 57),
            self::C10 => $this->computeSize(38, 40),

            self::LEGAL => $this->computeSize(8.5, 14),
            self::LETTER => $this->computeSize(8.5, 11),
            self::LEDGER => $this->computeSize(11, 17),
            self::EXECUTIVE => $this->computeSize(7.5, 10),
            self::ANSI_C => $this->computeSize(17, 22),
            self::ANSI_D => $this->computeSize(22, 34),
            self::ANSI_E => $this->computeSize(34, 44),
        };
    }

    /**
     * Gets the document unit.
     */
    public function getUnit(): PdfUnit
    {
        return match ($this) {
            self::A0,
            self::A1,
            self::A2,
            self::A3,
            self::A4,
            self::A5,
            self::A6,
            self::A7,
            self::A8,
            self::A9,
            self::A10,

            self::B0,
            self::B1,
            self::B2,
            self::B3,
            self::B4,
            self::B5,
            self::B6,
            self::B7,
            self::B8,
            self::B9,
            self::B10,

            self::C0,
            self::C1,
            self::C2,
            self::C3,
            self::C4,
            self::C5,
            self::C6,
            self::C7,
            self::C8,
            self::C9,
            self::C10 => PdfUnit::MILLIMETER,

            self::LEGAL,
            self::LETTER,
            self::LEDGER,
            self::EXECUTIVE,
            self::ANSI_C,
            self::ANSI_D,
            self::ANSI_E => PdfUnit::INCH,
        };
    }

    private function computeSize(float $width, float $height): PdfSize
    {
        $scaleFactor = $this->getUnit()->getScaleFactor();
        $width = \round($width * $scaleFactor, 2);
        $height = \round($height * $scaleFactor, 2);

        return PdfSize::instance($width, $height);
    }
}
