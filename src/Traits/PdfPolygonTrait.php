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

namespace fpdf\Traits;

use fpdf\Enums\PdfRectangleStyle;
use fpdf\PdfPoint;

/**
 * Trait to draw polygon.
 *
 *  The code is inspired from FPDF script
 *  <a href="https://www.fpdf.org/en/script/script60.php" target="_blank">Polygons</a>.
 *
 * @phpstan-require-extends \fpdf\PdfDocument
 */
trait PdfPolygonTrait
{
    /**
     * Draw a polygon with the given style.
     *
     * @param PdfPoint[]        $points the points to draw
     * @param PdfRectangleStyle $style  the style of rendering
     */
    public function polygon(array $points, PdfRectangleStyle $style = PdfRectangleStyle::BORDER): void
    {
        $type = 'm';
        $height = $this->getPageHeight();

        $output = '';
        foreach ($points as $point) {
            $output .= \sprintf('%.2F %.2F %s ', $this->scale($point->x), $this->scale($height - $point->y), $type);
            $type = 'l';
        }
        $output .= \strtolower($style->value);

        $this->out($output);
    }
}
