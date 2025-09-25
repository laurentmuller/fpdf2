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
    final public function __construct(public int $red, public int $green, public int $blue)
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
    public static function black(): static
    {
        return self::instance(0, 0, 0);
    }

    /**
     * Get the blue color.
     *
     * The value is RGB(0, 0, 255).
     */
    public static function blue(): static
    {
        return self::instance(0, 0, 255);
    }

    /**
     * Try to create an RGB color from the given string.
     *
     * Note: This function will ignore any non-hexadecimal characters it encounters.
     *
     * @param ?string $value the value to parse. A hexadecimal string
     *                       like <code>'FF8040'</code> or <code>'FFF'</code>
     *
     * @return static|null the RGB color, if applicable, null otherwise
     */
    public static function create(?string $value): ?static
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
    public static function darkGray(): static
    {
        return self::instance(169, 169, 169);
    }

    /**
     * Gets the dark-green color.
     *
     * The value is RGB(0, 128, 0).
     */
    public static function darkGreen(): static
    {
        return self::instance(0, 128, 0);
    }

    /**
     * Gets the dark-red color.
     *
     * The value is RGB(128, 0, 0).
     */
    public static function darkRed(): static
    {
        return self::instance(128, 0, 0);
    }

    #[\Override]
    public function equals(PdfColorInterface $other): bool
    {
        return $other instanceof self
            && $this->red === $other->red
            && $this->green === $other->green
            && $this->blue === $other->blue;
    }

    #[\Override]
    public function getOutput(): string
    {
        return \sprintf('%s %s', $this->getTag(), $this->getSuffix());
    }

    #[\Override]
    public function getTag(): string
    {
        // black?
        if ($this->isBlack()) {
            return \sprintf('%.3F', 0.0);
        }

        return \sprintf(
            '%.3F %.3F %.3F',
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
    public static function green(): static
    {
        return self::instance(0, 255, 0);
    }

    /**
     * Creates a new instance.
     *
     * @param int<0, 255> $red   the red component
     * @param int<0, 255> $green the green component
     * @param int<0, 255> $blue  the blue component
     */
    public static function instance(int $red, int $green, int $blue): static
    {
        return new static($red, $green, $blue);
    }

    /**
     * Gets the red color.
     *
     * The value is RGB(255, 0, 0).
     */
    public static function red(): static
    {
        return self::instance(255, 0, 0);
    }

    /**
     * Convert this color to a CMYK color.
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
     * Convert this color to a gray color.
     */
    public function toGrayColor(): PdfGrayColor
    {
        $red = 0.299 * (float) $this->red;
        $green = 0.587 * (float) $this->green;
        $blue = 0.114 * (float) $this->blue;
        /** @var int<0, 255> $level */
        $level = (int) ($red + $green + $blue);

        return PdfGrayColor::instance($level);
    }

    /**
     * Gets the white color.
     *
     * The value is RGB(255, 255, 255).
     */
    public static function white(): static
    {
        return self::instance(255, 255, 255);
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

    private static function createFrom3Chars(string $value): static
    {
        $red = self::hexdec(\str_repeat(\substr($value, 0, 1), 2));
        $green = self::hexdec(\str_repeat(\substr($value, 1, 1), 2));
        $blue = self::hexdec(\str_repeat(\substr($value, 2, 1), 2));

        return self::instance($red, $green, $blue);
    }

    private static function createFrom6Chars(string $value): static
    {
        $red = self::hexdec(\substr($value, 0, 2));
        $green = self::hexdec(\substr($value, 2, 2));
        $blue = self::hexdec(\substr($value, 4, 2));

        return self::instance($red, $green, $blue);
    }

    private function getSuffix(): string
    {
        return $this->isBlack() ? 'g' : 'rg';
    }

    /**
     * @return int<0, 255>
     */
    private static function hexdec(string $value): int
    {
        /** @var int<0, 255> */
        return (int) \hexdec($value);
    }

    private function isBlack(): bool
    {
        return 0 === $this->red && 0 === $this->green && 0 === $this->blue;
    }
}
