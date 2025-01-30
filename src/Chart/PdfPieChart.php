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

use fpdf\Enums\PdfRectangleStyle;
use fpdf\PdfDocument;
use fpdf\PdfException;
use fpdf\Traits\PdfSectorTrait;

/**
 * Chart with a list of sectors.
 */
class PdfPieChart extends AbstractPdfChart
{
    private bool $clockwise = true;
    private float $origin = 90;
    private PdfRectangleStyle $style = PdfRectangleStyle::BOTH;
    /**
     * @var array<string, PdfPieSector>
     */
    private array $values = [];

    /**
     * Adds the given sector.
     *
     * @throws PdfException if a sector already exists with the same title
     */
    public function addSector(PdfPieSector $sector): self
    {
        if (\array_key_exists($sector->getTitle(), $this->values)) {
            PdfException::format('The sector "%s" already exists.', $sector->getTitle());
        }

        $this->values[$sector->getTitle()] = $sector;

        return $this;
    }

    /**
     * @return float the origin,in degrees, of angles (0=right, 90=top, 180=left, 270=for bottom)
     */
    public function getOrigin(): float
    {
        return $this->origin;
    }

    /**
     * @return PdfRectangleStyle the style of sector rendering
     */
    public function getStyle(): PdfRectangleStyle
    {
        return $this->style;
    }

    /**
     * @return array<string, PdfPieSector>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return bool indicates whether to go clockwise (true) or counter-clockwise (false)
     */
    public function isClockwise(): bool
    {
        return $this->clockwise;
    }

    //    /**
    //     * @throws PdfException if the given parent does not use the {@link PdfSectorTrait}
    //     */
    //    public function render(PdfDocument $parent, ?float $x = null, ?float $y = null, ?float $width = null, ?float $height = null): void
    //    {
    //        if (!\in_array(PdfSectorTrait::class, \class_uses($parent), true)) {
    //            PdfException::format('The PDF document must use the "%s" trait.', PdfSectorTrait::class);
    //        }
    //
    //        $x ??= $parent->getX();
    //        $y ??= $parent->getY();
    //        $parent->setX($x);
    //        $width ??= $parent->getRemainingWidth();
    //        $height ??= $width;
    //        $radius = \min($width, $height) / 2.0;
    //        $centerX = $x + $width / 2.0;
    //        $centerY = $y + $height / 2.0;
    //        $total = $this->getTotal();
    //
    //        $startAngle = 0.0;
    //        foreach ($this->values as $sector) {
    //            $parent->setFillColor($sector->getColor());
    //            $endAngle = $startAngle + 360.0 * (float) $sector->getValue() / $total;
    //            $parent->sector(
    //                $centerX,
    //                $centerY,
    //                $radius,
    //                $startAngle,
    //                $endAngle,
    //                $this->style,
    //                $this->clockwise,
    //                $this->origin,
    //            );
    //            $startAngle = $endAngle;
    //        }
    //    }

    /**
     * @param bool $clockwise indicates whether to go clockwise (true) or counter-clockwise (false)
     */
    public function setClockwise(bool $clockwise): self
    {
        $this->clockwise = $clockwise;

        return $this;
    }

    /**
     * @param float $origin the origin,in degrees, of angles (0=right, 90=top, 180=left, 270=for bottom)
     */
    public function setOrigin(float $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * @param PdfRectangleStyle $style the style of sector rendering
     */
    public function setStyle(PdfRectangleStyle $style): self
    {
        $this->style = $style;

        return $this;
    }

    protected function getTotal(): float
    {
        return \array_sum(\array_map(fn (PdfPieSector $sector): float => $sector->getValue(), $this->values));
    }
}
