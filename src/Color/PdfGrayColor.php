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

namespace fpdf\Color;

use fpdf\Interfaces\PdfColorInterface;

/**
 * Represents a grayed (level) color.
 */
readonly class PdfGrayColor implements PdfColorInterface
{
    /**
     * @param int<0, 255> $level the gray level (0 to 255)
     */
    public function __construct(public int $level)
    {
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s(%d)',
            (new \ReflectionClass(self::class))->getShortName(),
            $this->level
        );
    }

    /**
     * Gets the black color.
     *
     * The value is Gray(0).
     */
    public static function black(): self
    {
        return new self(0);
    }

    #[\Override]
    public function equals(PdfColorInterface $other): bool
    {
        return $other instanceof self && $this->level === $other->level;
    }

    #[\Override]
    public function getOutput(): string
    {
        return \sprintf('%.3F g', (float) $this->level / 255.0);
    }

    /**
     * Create a new instance.
     *
     * @param int<0, 255> $level the gray level
     */
    public static function instance(int $level): self
    {
        return new self($level);
    }

    /**
     * Gets the white color.
     *
     * The value is Gray(255).
     */
    public static function white(): self
    {
        return new self(255);
    }
}
