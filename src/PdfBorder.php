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
     * Create a new instance.
     *
     * @param bool $left   a value indicating if the left border is set
     * @param bool $top    a value indicating if the top border is set
     * @param bool $right  a value indicating if the right border is set
     * @param bool $bottom a value indicating if the bottom border is set
     */
    public function __construct(
        private bool $left,
        private bool $top,
        private bool $right,
        private bool $bottom,
    ) {
    }

    /**
     * Create an instance with all borders set.
     */
    public static function all(): self
    {
        return new self(true, true, true, true);
    }

    /**
     * Create an instance with the bottom border only.
     */
    public static function bottom(): self
    {
        return new self(false, false, false, true);
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
        if ($this->isLeft()) {
            $parent->line($x, $y, $x, $bottom);
        }
        if ($this->isRight()) {
            $parent->line($right, $y, $right, $bottom);
        }
        if ($this->isTop()) {
            $parent->line($x, $y, $right, $y);
        }
        if ($this->isBottom()) {
            $parent->line($x, $bottom, $right, $bottom);
        }
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
     * Gets a value indicating if the bottom border is set.
     */
    public function isBottom(): bool
    {
        return $this->bottom;
    }

    /**
     * Gets a value indicating if the left border is set.
     */
    public function isLeft(): bool
    {
        return $this->left;
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
     * Gets a value indicating if the right border is set.
     */
    public function isRight(): bool
    {
        return $this->right;
    }

    /**
     * Gets a value indicating if the top border is set.
     */
    public function isTop(): bool
    {
        return $this->top;
    }

    /**
     * Create an instance with the left border only.
     */
    public static function left(): self
    {
        return new self(true, false, false, false);
    }

    /**
     * Create an instance with the left and right borders only.
     */
    public static function leftRight(): self
    {
        return new self(true, false, true, false);
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
        return new self(false, false, false, false);
    }

    /**
     * Create an instance with all borders that are set in this instance or in the other given instance.
     *
     * @see PdfBorder::merge()
     */
    public function or(self $other): self
    {
        return new self(
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
        return new self(false, false, true, false);
    }

    /**
     * Sets a value indicating if the bottom border is set.
     */
    public function setBottom(bool $bottom = true): self
    {
        $this->bottom = $bottom;

        return $this;
    }

    /**
     * Sets a value indicating if the left border is set.
     */
    public function setLeft(bool $left = true): self
    {
        $this->left = $left;

        return $this;
    }

    /**
     * Sets a value indicating if the right border is set.
     */
    public function setRight(bool $right = true): self
    {
        $this->right = $right;

        return $this;
    }

    /**
     * Sets a value indicating if the top border is set.
     */
    public function setTop(bool $top = true): self
    {
        $this->top = $top;

        return $this;
    }

    /**
     * Create an instance with the top border only.
     */
    public static function top(): self
    {
        return new self(false, true, false, false);
    }

    /**
     * Create an instance with the top and bottom borders.
     */
    public static function topBottom(): self
    {
        return new self(false, true, false, true);
    }
}
