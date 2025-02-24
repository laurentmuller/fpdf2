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
 * Represent a point.
 */
class PdfPoint
{
    /**
     * @param float $x the abscissa
     * @param float $y the ordinate
     */
    public function __construct(public float $x, public float $y)
    {
    }

    /**
     * Returns these x and y values as an array.
     *
     * @return array{0: float, 1: float}
     */
    public function asArray(): array
    {
        return [$this->x, $this->y];
    }

    /**
     * Returns if the given point is equal to this instance.
     *
     * To be equal, the abscissa and the ordinate values must be equal
     */
    public function equals(self $other): bool
    {
        return ($this === $other) || ($this->x === $other->x && $this->y === $other->y);
    }

    /**
     * Creates a new instance.
     *
     * @param float $x the abscissa
     * @param float $y the ordinate
     */
    public static function instance(float $x, float $y): self
    {
        return new self($x, $y);
    }

    /**
     * Creates a new point with the given scale factor.
     *
     * @param float $scaleFactor the scale factor to apply (multiply by) to the x and y properties
     */
    public function scale(float $scaleFactor): self
    {
        return self::instance($this->x * $scaleFactor, $this->y * $scaleFactor);
    }

    /**
     * Gets a new instance where x and y are swapped.
     */
    public function swap(): self
    {
        return self::instance($this->y, $this->x);
    }
}
