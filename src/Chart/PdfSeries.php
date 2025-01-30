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

namespace fpdf\Chart;

use fpdf\Interfaces\PdfColorInterface;

/**
 * Contains a color and an array of values used to draw series.
 */
class PdfSeries implements \Countable
{
    /**
     * The color.
     */
    private PdfColorInterface $color;

    /**
     * @var callable(float|int):string|null
     */
    private mixed $formatter;

    private string $title;

    /**
     * @var float[]|int[]
     */
    private array $values;

    /**
     * @param float[]|int[]                   $values
     * @param callable(float|int):string|null $formatter
     */
    public function __construct(
        string $title,
        PdfColorInterface $color,
        array $values = [],
        ?callable $formatter = null
    ) {
        $this->title = $title;
        $this->color = $color;
        $this->values = $values;
        $this->formatter = $formatter;
    }

    /**
     * @return int<0, max> the number of values
     */
    public function count(): int
    {
        return \count($this->values);
    }

    /**
     * Formats the given value.
     *
     * If a formatter is defined, it is used; otherwise cast the value to a string.
     *
     * @param float|int $value the value to format
     */
    public function formatValue(float|int $value): string
    {
        if (\is_callable($this->formatter)) {
            return \call_user_func($this->formatter, $value);
        }

        return (string) $value;
    }

    /**
     * Format this values.
     *
     * If a formatter is defined, it is used; otherwise cast the values to a string.
     *
     * @return string[]
     */
    public function formatValues(): array
    {
        return \array_map(fn (float|int $value): string => $this->formatValue($value), $this->values);
    }

    public function getColor(): PdfColorInterface
    {
        return $this->color;
    }

    /**
     * Gts the function to format values.
     *
     * @return callable(float|int):string|null
     */
    public function getFormatter(): mixed
    {
        return $this->formatter;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return float[]|int[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function isEmpty(): bool
    {
        return [] === $this->values;
    }

    public function setColor(PdfColorInterface $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Sets the function to format values.
     *
     * @param callable(float|int):string|null $formatter
     */
    public function setFormatter(?callable $formatter): self
    {
        $this->formatter = $formatter;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param float[]|int[] $values
     */
    public function setValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }
}
