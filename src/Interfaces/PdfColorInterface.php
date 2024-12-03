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
 * Classes implementing this interface provide stroking color.
 */
interface PdfColorInterface extends \Stringable
{
    /**
     * Returns if the given color is equal to this instance.
     *
     * @param PdfColorInterface $other the color to compare to
     *
     * @return bool true if equal
     */
    public function equals(self $other): bool;

    /**
     * Gets the operation color.
     */
    public function getColor(): string;
}
