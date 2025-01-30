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

/**
 * Chart with a series of bars.
 */
class PdfBarChart extends AbstractPdfSeriesChart
{
    private bool $stacked = true;

    /**
     * @return bool true if series are stacked; false if render side by side
     */
    public function isStacked(): bool
    {
        return $this->stacked;
    }

    /**
     * @param bool $stacked true if series are stacked; false if render side by side
     */
    public function setStacked(bool $stacked): self
    {
        $this->stacked = $stacked;

        return $this;
    }
}
