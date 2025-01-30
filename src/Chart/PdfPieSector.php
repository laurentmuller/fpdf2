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
 * Contains a value and color used to draw a pie chart.
 */
class PdfPieSector
{
    private PdfColorInterface $color;
    private string $title;
    private float|int $value;

    public function __construct(string $title, float|int $value, PdfColorInterface $color)
    {
        $this->title = $title;
        $this->value = $value;
        $this->color = $color;
    }

    public function getColor(): PdfColorInterface
    {
        return $this->color;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getValue(): float|int
    {
        return $this->value;
    }

    public function setColor(PdfColorInterface $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setValue(float|int $value): self
    {
        $this->value = $value;

        return $this;
    }
}
