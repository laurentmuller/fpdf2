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
 * Represent a margins.
 */
class PdfMargins
{
    /**
     * @param float $left   the left margin
     * @param float $top    the top margin
     * @param float $right  the right margin
     * @param float $bottom the bottom margin
     */
    public function __construct(
        public float $left,
        public float $top,
        public float $right,
        public float $bottom
    ) {}

    /**
     * Returns these left, top, right and bottom values as an array.
     *
     * @return array{0: float, 1: float, 2: float, 3: float}
     */
    public function asArray(): array
    {
        return [$this->left, $this->top, $this->right, $this->bottom];
    }

    /**
     * Returns if the given margin is equal to this instance.
     *
     * To be equal, the left, the top, the right and the bottom values must be equal
     */
    public function equals(self $other): bool
    {
        return ($this === $other) || ($this->left === $other->left && $this->top === $other->top
                && $this->right === $other->right && $this->bottom === $other->bottom);
    }

    /**
     * Create a new instance.
     *
     * @param float $left   the left margin
     * @param float $top    the top margin
     * @param float $right  the right margin
     * @param float $bottom the bottom margin
     */
    public static function instance(
        float $left = 0.0,
        float $top = 0.0,
        float $right = 0.0,
        float $bottom = 0.0
    ): self {
        return new self($left, $top, $right, $bottom);
    }

    /**
     * Sets all four margins to 0.
     */
    public function reset(): self
    {
        return $this->setMargins(0);
    }

    /**
     * Sets all four margins to the given value.
     */
    public function setMargins(float $margin): self
    {
        $this->left = $margin;
        $this->top = $margin;
        $this->right = $margin;
        $this->bottom = $margin;

        return $this;
    }
}
