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

namespace fpdf\Interfaces;

/**
 * Class implementing this interface provides color for drawing, filling and text of the PDF document.
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
}
