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
 *
 * The gray level is specified as an integer between 0 and 255.
 */
readonly class PdfGrayColor implements PdfColorInterface
{
    /**
     * @param int<0, 255> $level the gray level (0 to 255)
     */
    public function __construct(public int $level) {}

    #[\Override]
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
        return \sprintf('%s g', $this->getTag());
    }

    #[\Override]
    public function getTag(): string
    {
        return \sprintf('%.3F', $this->asFloat());
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

    #[\Override]
    public function toCmykColor(): PdfCmykColor
    {
        /** @var int<0, 100> $black */
        $black = (int) (100.0 * $this->asFloat());

        return PdfCmykColor::instance(0, 0, 0, $black);
    }

    #[\Override]
    public function toGrayColor(): self
    {
        return $this;
    }

    #[\Override]
    public function toRgbColor(): PdfRgbColor
    {
        return PdfRgbColor::instance($this->level, $this->level, $this->level);
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

    private function asFloat(): float
    {
        return (float) $this->level / 255.0;
    }
}
