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

namespace fpdf\Interfaces;

use fpdf\Color\PdfCmykColor;
use fpdf\Color\PdfGrayColor;
use fpdf\Color\PdfRgbColor;

/**
 * Class implementing this interface provides color for drawing, filling, and text of the PDF document.
 */
interface PdfColorInterface extends \Stringable
{
    /**
     * Returns if the given color is equal to this instance.
     */
    public function equals(self $other): bool;

    /**
     * Gets this color, as string, when output to the document.
     */
    public function getOutput(): string;

    /**
     * Gets this color, as string, when required to be output to the dictionary.
     */
    public function getTag(): string;

    /**
     * Gets this instance as an CMYK color.
     */
    public function toCmykColor(): PdfCmykColor;

    /**
     * Gets this instance as a Gray color.
     */
    public function toGrayColor(): PdfGrayColor;

    /**
     * Gets this instance as an RGB color.
     */
    public function toRgbColor(): PdfRgbColor;
}
