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
 * Represent a gray color.
 */
readonly class PdfGrayColor implements PdfColorInterface
{
    /**
     * @param int<0, 255> $value the gray scale from 0 (black) to 255 (white)
     */
    public function __construct(public int $value)
    {
    }

    public function __toString(): string
    {
        return \sprintf('PdfGrayColor(%d)', $this->value);
    }

    public function equals(PdfColorInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function getColor(): string
    {
        $value = (float) $this->value / 255.0;

        return \sprintf('%.3F G', $value);
    }

    /**
     * Creates a new instance.
     *
     * @param int<0, 255> $value the gray scale from 0 (black) to 255 (white)
     */
    public static function instance(int $value): self
    {
        return new self($value);
    }
}
