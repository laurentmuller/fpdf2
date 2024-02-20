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
 * Represent a size.
 */
class PdfSize
{
    /**
     * @param float $width  the width
     * @param float $height the height
     */
    public function __construct(public float $width, public float $height)
    {
    }

    /**
     * Returns this width and height values as array.
     *
     * @return array{0: float, 1: float}
     */
    public function asArray(): array
    {
        return [$this->width, $this->height];
    }

    /**
     * Returns if the given size is equal to this instance.
     *
     * To be equal, the width and the height values must equal
     */
    public function equals(self $other): bool
    {
        return ($this === $other) || ($this->width === $other->width && $this->height === $other->height);
    }

    /**
     * Create a new instance.
     *
     * @param float $width  the width
     * @param float $height the height
     */
    public static function instance(float $width, float $height): self
    {
        return new self($width, $height);
    }

    /**
     * Creates a new size with the given scale factor.
     *
     * @param float $scaleFactor the scale factor to apply (multiply by) to the width and height properties
     */
    public function scale(float $scaleFactor): self
    {
        return new self($this->width * $scaleFactor, $this->height * $scaleFactor);
    }

    /**
     * Gets a new instance where height and width are swapped.
     */
    public function swap(): self
    {
        return self::instance($this->height, $this->width);
    }
}
