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
 * Represents an RGB (red-green-blue) color.
 */
readonly class PdfRgbColor implements PdfColorInterface
{
    /**
     * @param int<0, 255> $red   the red component (0 to 255)
     * @param int<0, 255> $green the green component (0 to 255)
     * @param int<0, 255> $blue  the blue component (0 to 255)
     */
    public function __construct(public int $red, public int $green, public int $blue)
    {
    }

    public function __toString(): string
    {
        return \sprintf(
            '%s(%d,%d,%d)',
            (new \ReflectionClass(self::class))->getShortName(),
            $this->red,
            $this->green,
            $this->blue
        );
    }

    /**
     * Gets the hexadecimal representation of these values.
     *
     * @param string $prefix the optional prefix to prepend
     *
     * @return string the hexadecimal value as six lower case characters (like <code>'ff8040'</code>)
     */
    public function asHex(string $prefix = ''): string
    {
        return \sprintf('%s%02x%02x%02x', $prefix, $this->red, $this->green, $this->blue);
    }

    /**
     * Gets the black color.
     *
     * The value is RGB(0, 0, 0).
     */
    public static function black(): self
    {
        return new self(0, 0, 0);
    }

    /**
     * Get the blue color.
     *
     * The value is RGB(0, 0, 255).
     */
    public static function blue(): self
    {
        return new self(0, 0, 255);
    }

    /**
     * Try to create an RGB color from the given string.
     *
     * Note: This function will ignore any non-hexadecimal characters it encounters.
     *
     * @param ?string $value the value to parse. A hexadecimal string
     *                       like <code>'FF8040'</code> or <code>'FFF'</code>
     *
     * @return PdfRgbColor|null the RGB color, if applicable, null otherwise
     */
    public static function create(?string $value): ?self
    {
        if (null === $value || '' === $value) {
            return null;
        }

        $value = (string) \preg_replace('/[^0-9A-F]/i', '', $value);

        return match (\strlen($value)) {
            3 => self::createFrom3Chars($value),
            6 => self::createFrom6Chars($value),
            default => null,
        };
    }

    /**
     * Gets the dark-gray color.
     *
     * The value is RGB(169, 169, 169).
     */
    public static function darkGray(): self
    {
        return new self(169, 169, 169);
    }

    /**
     * Gets the dark-green color.
     *
     * The value is RGB(0, 128, 0).
     */
    public static function darkGreen(): self
    {
        return new self(0, 128, 0);
    }

    /**
     * Gets the dark-red color.
     *
     * The value is RGB(128, 0, 0).
     */
    public static function darkRed(): self
    {
        return new self(128, 0, 0);
    }

    public function equals(PdfColorInterface $other): bool
    {
        return $other instanceof self
            && $this->red === $other->red
            && $this->green === $other->green
            && $this->blue === $other->blue;
    }

    public function getOutput(): string
    {
        // black?
        if (0 === $this->red && 0 === $this->green && 0 === $this->blue) {
            return '0.000 g';
        }

        return \sprintf(
            '%.3F %.3F %.3F rg',
            $this->asFloat($this->red),
            $this->asFloat($this->green),
            $this->asFloat($this->blue)
        );
    }

    /**
     * Gets the green color.
     *
     * The value is RGB(0, 255, 0).
     */
    public static function green(): self
    {
        return new self(0, 255, 0);
    }

    /**
     * @param int<0, 255> $red   the red component
     * @param int<0, 255> $green the green component
     * @param int<0, 255> $blue  the blue component
     */
    public static function instance(int $red, int $green, int $blue): self
    {
        return new self($red, $green, $blue);
    }

    /**
     * Gets the red color.
     *
     * The value is RGB(255, 0, 0).
     */
    public static function red(): self
    {
        return new self(255, 0, 0);
    }

    /**
     * Convert this color to a CMYK color.
     *
     * @see PdfCmykColor::toRgbColor()
     */
    public function toCmykColor(): PdfCmykColor
    {
        $red = $this->asFloat($this->red);
        $green = $this->asFloat($this->green);
        $blue = $this->asFloat($this->blue);
        $black = 1.0 - \max($red, $green, $blue);

        $divisor = 1.0 - $black;
        $cyan = (1.0 - $red - $black) / $divisor;
        $magenta = (1.0 - $green - $black) / $divisor;
        $yellow = (1.0 - $blue - $black) / $divisor;

        return PdfCmykColor::instance(
            $this->asInt($cyan),
            $this->asInt($magenta),
            $this->asInt($yellow),
            $this->asInt($black)
        );
    }

    /**
     * Gets the white color.
     *
     * The value is RGB(255, 255, 255).
     */
    public static function white(): self
    {
        return new self(255, 255, 255);
    }

    private function asFloat(int $value): float
    {
        return (float) $value / 255.0;
    }

    /**
     * @return int<0, 100>
     */
    private function asInt(float $value): int
    {
        /** @var int<0 ,100> */
        return (int) \round($value * 100.0);
    }

    private static function createFrom3Chars(string $value): self
    {
        $red = self::hexdec(\str_repeat(\substr($value, 0, 1), 2));
        $green = self::hexdec(\str_repeat(\substr($value, 1, 1), 2));
        $blue = self::hexdec(\str_repeat(\substr($value, 2, 1), 2));

        return self::instance($red, $green, $blue);
    }

    private static function createFrom6Chars(string $value): self
    {
        $red = self::hexdec(\substr($value, 0, 2));
        $green = self::hexdec(\substr($value, 2, 2));
        $blue = self::hexdec(\substr($value, 4, 2));

        return self::instance($red, $green, $blue);
    }

    /**
     * @return int<0, 255>
     */
    private static function hexdec(string $value): int
    {
        /** @var int<0, 255> */
        return (int) \hexdec($value);
    }
}
