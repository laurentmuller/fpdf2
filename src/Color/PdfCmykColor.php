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
 * Represents an CMYK (cyan-magenta-yellow-black) color.
 */
readonly class PdfCmykColor implements PdfColorInterface
{
    /**
     * @param int<0, 100> $cyan    the cyan component (0 to 100)
     * @param int<0, 100> $magenta the magenta component (0 to 100)
     * @param int<0, 100> $yellow  the yellow component (0 to 100)
     * @param int<0, 100> $black   the black component (0 to 100)
     */
    public function __construct(public int $cyan, public int $magenta, public int $yellow, public int $black)
    {
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s(%d,%d,%d,%d)',
            (new \ReflectionClass(self::class))->getShortName(),
            $this->cyan,
            $this->magenta,
            $this->yellow,
            $this->black
        );
    }

    public function equals(PdfColorInterface $other): bool
    {
        return $other instanceof self
            && $this->cyan === $other->cyan
            && $this->magenta === $other->magenta
            && $this->yellow === $other->yellow
            && $this->black === $other->black;
    }

    public function getOutput(): string
    {
        return \sprintf(
            '%.3F %.3F %.3F %.3F k',
            $this->asFloat($this->cyan),
            $this->asFloat($this->magenta),
            $this->asFloat($this->yellow),
            $this->asFloat($this->black)
        );
    }

    /**
     * Create a new instance.
     *
     * @param int<0, 100> $cyan    the cyan component (0 to 100)
     * @param int<0, 100> $magenta the magenta component (0 to 100)
     * @param int<0, 100> $yellow  the yellow component (0 to 100)
     * @param int<0, 100> $black   the black component (0 to 100)
     */
    public static function instance(int $cyan, int $magenta, int $yellow, int $black): self
    {
        return new self($cyan, $magenta, $yellow, $black);
    }

    /**
     * Convert this color to an RGB color.
     *
     * @see PdfRgbColor::toCmykColor()
     */
    public function toRgbColor(): PdfRgbColor
    {
        $cyan = $this->asFloat($this->cyan);
        $black = $this->asFloat($this->black);
        $magenta = $this->asFloat($this->magenta);
        $yellow = $this->asFloat($this->yellow);

        $multiplier = 1.0 - $black;
        $red = $this->asInt($cyan, $multiplier);
        $green = $this->asInt($magenta, $multiplier);
        $blue = $this->asInt($yellow, $multiplier);

        return PdfRgbColor::instance($red, $green, $blue);
    }

    private function asFloat(int $value): float
    {
        return (float) $value / 100.0;
    }

    /**
     * @return int<0, 255>
     */
    private function asInt(float $value, float $multiplier): int
    {
        /** @var int<0, 255> */
        return (int) \round(255.0 * (1.0 - $value) * $multiplier);
    }
}
