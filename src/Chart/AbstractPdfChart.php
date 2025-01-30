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

use fpdf\PdfDocument;
use fpdf\PdfException;

/**
 * Abstract chart.
 */
abstract class AbstractPdfChart implements \Countable
{
    /**
     * @var string[]
     */
    private array $categories;

    private ?string $title = null;

    /**
     * @param string[] $categories
     */
    public function __construct(array $categories = [])
    {
        $this->categories = $categories;
    }

    /**
     * @return int<0, max> the number of categories
     */
    public function count(): int
    {
        return \count($this->categories);
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Render this chart.
     *
     * @param PdfDocument $parent the parent document
     * @param ?float      $x      the abscissa or null to use current abscissa
     * @param ?float      $y      the ordinate or null to use current ordinate
     * @param ?float      $width  the width or null to use remaining width
     * @param ?float      $height the height or null to compute it
     */
    public function render(
        PdfDocument $parent,
        ?float $x = null,
        ?float $y = null,
        ?float $width = null,
        ?float $height = null,
    ): void {
    }

    /**
     * @param string[] $categories
     */
    public function setCategories(array $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Validate if the given series contains the same number of values as this number of categories.
     *
     * @throws PdfException if not match
     */
    protected function validateSeries(PdfSeries $series): void
    {
        $actual = \count($series);
        $expected = \count($this);
        if ($actual !== $expected) {
            PdfException::format('Invalid number of values: %d, expected: %d.', $actual, $expected);
        }
    }
}
