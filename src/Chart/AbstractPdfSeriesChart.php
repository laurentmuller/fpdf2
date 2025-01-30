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

use fpdf\PdfException;

/**
 * Abstract chart that contains a list of series.
 */
abstract class AbstractPdfSeriesChart extends AbstractPdfChart
{
    /**
     * The list of series.
     *
     * @var array<string, PdfSeries>
     */
    protected array $values = [];

    /**
     * Adds the given series.
     *
     * @throws PdfException if a series already exists with the same title
     */
    public function addSeries(PdfSeries $series): self
    {
        if (\array_key_exists($series->getTitle(), $this->values)) {
            PdfException::format('The series "%s" already exists.', $series->getTitle());
        }
        $this->values[$series->getTitle()] = $series;

        return $this;
    }

    /**
     * @return array<string, PdfSeries>
     */
    public function getValues(): array
    {
        return $this->values;
    }
}
