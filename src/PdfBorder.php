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
 * Define border style for cells.
 *
 * @see PdfDocument::cell()
 * @see PdfDocument::multiCell()
 * @see PdfDocument::rect()
 */
class PdfBorder
{
    /**
     * @param bool $left   a value indicating if the left border is set
     * @param bool $top    a value indicating if the top border is set
     * @param bool $right  a value indicating if the right border is set
     * @param bool $bottom a value indicating if the bottom border is set
     */
    public function __construct(
        public bool $left,
        public bool $top,
        public bool $right,
        public bool $bottom,
    ) {}

    /**
     * Create an instance with all borders set.
     */
    public static function all(): self
    {
        return self::instance(true, true, true, true);
    }

    /**
     * Create an instance with the bottom border only.
     */
    public static function bottom(): self
    {
        return self::instance(false, false, false, true);
    }

    /**
     * Draw borders, if applicable, to the given document using the current draw color and line width.
     *
     * @param PdfDocument  $parent the parent document to draw borders to
     * @param PdfRectangle $bounds the border bounds
     */
    public function draw(PdfDocument $parent, PdfRectangle $bounds): void
    {
        if ($this->isNone()) {
            return;
        }

        if ($this->isAll()) {
            $parent->rectangle($bounds);

            return;
        }

        // draw each applicable border side
        $x = $bounds->x;
        $y = $bounds->y;
        $right = $bounds->right();
        $bottom = $bounds->bottom();
        if ($this->left) {
            $parent->line($x, $y, $x, $bottom);
        }
        if ($this->right) {
            $parent->line($right, $y, $right, $bottom);
        }
        if ($this->top) {
            $parent->line($x, $y, $right, $y);
        }
        if ($this->bottom) {
            $parent->line($x, $bottom, $right, $bottom);
        }
    }

    /**
     * Returns if the given border is equal to this instance.
     *
     * To be equal, the four sides must be equal
     */
    public function equals(self $other): bool
    {
        return ($this === $other) || (
            $this->left === $other->left
            && $this->top === $other->top
            && $this->right === $other->right
            && $this->bottom === $other->bottom
        );
    }

    /**
     * Create a new instance.
     *
     * @param bool $left   a value indicating if the left border is set
     * @param bool $top    a value indicating if the top border is set
     * @param bool $right  a value indicating if the right border is set
     * @param bool $bottom a value indicating if the bottom border is set
     */
    public static function instance(
        bool $left,
        bool $top,
        bool $right,
        bool $bottom,
    ): self {
        return new self($left, $top, $right, $bottom);
    }

    /**
     * Gets a value indicating if all borders are set.
     */
    public function isAll(): bool
    {
        return $this->left
            && $this->right
            && $this->top
            && $this->bottom;
    }

    /**
     * Gets a value indicating if at least one border is set.
     */
    public function isAny(): bool
    {
        return $this->left
            || $this->right
            || $this->top
            || $this->bottom;
    }

    /**
     * Gets a value indicating if no border is set.
     */
    public function isNone(): bool
    {
        return !$this->left
            && !$this->right
            && !$this->top
            && !$this->bottom;
    }

    /**
     * Create an instance with the left border only.
     */
    public static function left(): self
    {
        return self::instance(true, false, false, false);
    }

    /**
     * Create an instance with the left and right borders only.
     */
    public static function leftRight(): self
    {
        return self::instance(true, false, true, false);
    }

    /**
     * Create an instance with all borders that are set in the given borders.
     *
     * @see PdfBorder::or()
     */
    public static function merge(self ...$borders): self
    {
        $result = self::none();
        foreach ($borders as $border) {
            $result = $result->or($border);
        }

        return $result;
    }

    /**
     * Create an instance without a border.
     */
    public static function none(): self
    {
        return self::instance(false, false, false, false);
    }

    /**
     * Create an instance with all borders except the bottom.
     */
    public static function notBottom(): self
    {
        return self::instance(true, true, true, false);
    }

    /**
     * Create an instance with all borders except the left.
     */
    public static function notLeft(): self
    {
        return self::instance(false, true, true, true);
    }

    /**
     * Create an instance with all borders except the right.
     */
    public static function notRight(): self
    {
        return self::instance(true, true, false, true);
    }

    /**
     * Create an instance with all borders except the top.
     */
    public static function notTop(): self
    {
        return self::instance(true, false, true, true);
    }

    /**
     * Create an instance with all borders that are set in this instance or in the other given instance.
     *
     * @see PdfBorder::merge()
     */
    public function or(self $other): self
    {
        return self::instance(
            $this->left || $other->left,
            $this->top || $other->top,
            $this->right || $other->right,
            $this->bottom || $other->bottom,
        );
    }

    /**
     * Create an instance with the right border only.
     */
    public static function right(): self
    {
        return self::instance(false, false, true, false);
    }

    /**
     * Create an instance with the top border only.
     */
    public static function top(): self
    {
        return self::instance(false, true, false, false);
    }

    /**
     * Create an instance with the top and bottom borders.
     */
    public static function topBottom(): self
    {
        return self::instance(false, true, false, true);
    }
}
