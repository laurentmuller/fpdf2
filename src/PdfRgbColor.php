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

use fpdf\Interfaces\PdfColorInterface;

/**
 * Represent an RGB color.
 */
readonly class PdfRgbColor implements PdfColorInterface
{
    /**
     * @param int<0, 255> $red   the red component
     * @param int<0, 255> $green the green component
     * @param int<0, 255> $blue  the blue component
     */
    public function __construct(public int $red, public int $green, public int $blue)
    {
    }

    public function __toString(): string
    {
        return \sprintf('PdfRgbColor(%d, %d, %d)', $this->red, $this->green, $this->blue);
    }

    public function equals(PdfColorInterface $other): bool
    {
        return $other instanceof self
            && $this->red === $other->red
            && $this->green === $other->green
            && $this->blue === $other->blue;
    }

    public function getColor(): string
    {
        $red = (float) $this->red / 255.0;
        $green = (float) $this->green / 255.0;
        $blue = (float) $this->blue / 255.0;

        return \sprintf('%.3F %.3F %.3F RG', $red, $green, $blue);
    }

    /**
     * Creates a new instance.
     *
     * @param int<0, 255> $red   the red component
     * @param int<0, 255> $green the green component
     * @param int<0, 255> $blue  the blue component
     */
    public static function instance(int $red, int $green, int $blue): self
    {
        return new self($red, $green, $blue);
    }
}
