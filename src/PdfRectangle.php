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
 * Represent a rectangle with an origine and a size.
 */
class PdfRectangle
{
    /**
     * @param float $x      the abscissa
     * @param float $y      the ordinate
     * @param float $width  the width
     * @param float $height the height
     */
    public function __construct(public float $x, public float $y, public float $width, public float $height)
    {
    }

    /**
     * Returns these x, y, width and height values as an array.
     *
     * @return array{0: float, 1: float, 2: float, 3: float}
     */
    public function asArray(): array
    {
        return [$this->x, $this->y, $this->width, $this->height];
    }

    /**
     * Gets the bottom coordinate.
     */
    public function bottom(): float
    {
        return $this->y + $this->height;
    }

    /**
     * Determines if the specified point is contained within this rectangle.
     *
     * @param PdfPoint $point the point to test
     *
     * @return bool true if the point is contained within this rectangle; false otherwise
     */
    public function containsPoint(PdfPoint $point): bool
    {
        return $this->containsXY($point->x, $point->y);
    }

    /**
     * Determines if the specified point is contained within this rectangle.
     *
     * @param float $x the x coordinate of the point to test
     * @param float $y the y coordinate of the point to test
     *
     * @return bool true if the point is contained within this rectangle; false otherwise
     */
    public function containsXY(float $x, float $y): bool
    {
        return $x >= $this->x && $y >= $this->y
            && $x < $this->right() && $y < $this->bottom();
    }

    /**
     * Returns if the given rectangle is equal to this instance.
     *
     * To be equal, the abscissa, the ordinate, the width and the height values must be equal
     */
    public function equals(self $other): bool
    {
        return ($this === $other) || ($this->x === $other->x && $this->y === $other->y
            && $this->width === $other->width && $this->height === $other->height);
    }

    /**
     * Gets the origin.
     */
    public function getOrigin(): PdfPoint
    {
        return new PdfPoint($this->x, $this->y);
    }

    /**
     * Gets the size.
     */
    public function getSize(): PdfSize
    {
        return new PdfSize($this->width, $this->height);
    }

    /**
     * Sets the left indent.
     *
     * Do nothing if the indent is smaller than or equal to 0.
     *
     * @param float $indent the indent
     */
    public function indent(float $indent): self
    {
        if ($indent > 0) {
            $this->x += $indent;
            $this->width -= $indent;
        }

        return $this;
    }

    /**
     * Enlarges this rectangle by the specified amount.
     *
     * @param float $value the amount to inflate horizontally and vertically
     *
     * @return self this instance
     */
    public function inflate(float $value): self
    {
        return $this->inflateXY($value, $value);
    }

    /**
     * Enlarges this rectangle horizontally by the specified amount.
     *
     * @param float $value how much to inflate horizontally
     *
     * @return self this instance
     */
    public function inflateX(float $value): self
    {
        return $this->inflateXY($value, 0);
    }

    /**
     * Enlarges this rectangle by the specified amount.
     *
     * @param float $dx how much to inflate horizontally
     * @param float $dy how much to inflate vertically
     *
     * @return self this instance
     */
    public function inflateXY(float $dx, float $dy): self
    {
        $this->x -= $dx;
        $this->y -= $dy;
        $this->width += 2.0 * $dx;
        $this->height += 2.0 * $dy;

        return $this;
    }

    /**
     * Enlarges this rectangle vertically by the specified amount.
     *
     * @param float $value how much to inflate vertically
     *
     * @return self this instance
     */
    public function inflateY(float $value): self
    {
        return $this->inflateXY(0, $value);
    }

    /**
     * Create a new instance.
     *
     * @param float $x      the abscissa
     * @param float $y      the ordinate
     * @param float $width  the width
     * @param float $height the height
     */
    public static function instance(float $x, float $y, float $width, float $height): self
    {
        return new self($x, $y, $width, $height);
    }

    /**
     * Determines if this rectangle intersects with the other given rectangle.
     *
     * @param PdfRectangle $other the rectangle to test
     *
     * @return bool true if there is any intersection, false otherwise
     */
    public function intersect(self $other): bool
    {
        return ($other->x < $this->right())
            && ($other->y < $this->bottom())
            && ($other->right() > $this->x)
            && ($other->bottom() > $this->y);
    }

    /**
     * Gets the right coordinate.
     */
    public function right(): float
    {
        return $this->x + $this->width;
    }

    /**
     * Creates a new rectangle with the given scale factor.
     *
     * @param float $scaleFactor the scale factor to apply (multiply by) to the x, y, width and height properties
     */
    public function scale(float $scaleFactor): self
    {
        return self::instance(
            $this->x * $scaleFactor,
            $this->y * $scaleFactor,
            $this->width * $scaleFactor,
            $this->height * $scaleFactor
        );
    }

    /**
     * Sets the bottom.
     *
     * @return self this instance
     */
    public function setBottom(float $bottom): self
    {
        $this->height = $bottom - $this->y;

        return $this;
    }

    /**
     * Sets the origin.
     */
    public function setOrigin(PdfPoint $origin): self
    {
        $this->x = $origin->x;
        $this->y = $origin->y;

        return $this;
    }

    /**
     * Sets the right.
     *
     * @return self this instance
     */
    public function setRight(float $right): self
    {
        $this->width = $right - $this->x;

        return $this;
    }

    /**
     * Sets the size.
     */
    public function setSize(PdfSize $size): self
    {
        $this->width = $size->width;
        $this->height = $size->height;

        return $this;
    }

    /**
     * Gets a rectangle that contains the union of this instance and the given other rectangle.
     *
     * @param PdfRectangle $other the rectangle to union
     */
    public function union(self $other): self
    {
        $x = \min($this->x, $other->x);
        $y = \min($this->y, $other->y);
        $right = \max($this->right(), $other->right());
        $bottom = \max($this->bottom(), $other->bottom());

        return self::instance($x, $y, $right - $x, $bottom - $y);
    }
}
