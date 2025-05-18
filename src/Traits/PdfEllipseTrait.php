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
use fpdf\PdfDocument;

/**
 * Trait to draw circles and ellipses.
 *
 *  The code is inspired from FPDF script
 *  <a href="http://www.fpdf.org/en/script/script6.php" target="_blank">Circles and ellipses</a>.
 *
 * @phpstan-require-extends PdfDocument
 */
trait PdfEllipseTrait
{
    /**
     * Draw a circle.
     *
     * It can be drawn (border only), filled (with no border) or both.
     *
     * @param float             $x     the abscissa of the center
     * @param float             $y     the ordinate of the center
     * @param float             $r     the radius
     * @param PdfRectangleStyle $style the style of rendering
     */
    public function circle(
        float $x,
        float $y,
        float $r,
        PdfRectangleStyle $style = PdfRectangleStyle::BORDER
    ): void {
        $this->ellipse($x, $y, $r, $r, $style);
    }

    /**
     * Draw an ellipse.
     *
     * It can be drawn (border only), filled (with no border) or both.
     *
     * @param float             $x     the abscissa of the center
     * @param float             $y     the ordinate of the center
     * @param float             $rx    the horizontal radius
     * @param float             $ry    the vertical radius
     * @param PdfRectangleStyle $style the style of rendering
     */
    public function ellipse(
        float $x,
        float $y,
        float $rx,
        float $ry,
        PdfRectangleStyle $style = PdfRectangleStyle::BORDER
    ): void {
        $lx = 4.0 / 3.0 * (\M_SQRT2 - 1.0) * $rx;
        $ly = 4.0 / 3.0 * (\M_SQRT2 - 1.0) * $ry;

        $this->outf(
            '%.2F %.2F m %.2F %.2F %.2F %.2F %.2F %.2F c',
            $this->scale($x + $rx),
            $this->scaleY($y),
            $this->scale($x + $rx),
            $this->scaleY($y - $ly),
            $this->scale($x + $lx),
            $this->scaleY($y - $ry),
            $this->scale($x),
            $this->scaleY($y - $ry)
        );
        $this->outf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            $this->scale($x - $lx),
            $this->scaleY($y - $ry),
            $this->scale($x - $rx),
            $this->scaleY($y - $ly),
            $this->scale($x - $rx),
            $this->scaleY($y)
        );
        $this->outf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            $this->scale($x - $rx),
            $this->scaleY($y + $ly),
            $this->scale($x - $lx),
            $this->scaleY($y + $ry),
            $this->scale($x),
            $this->scaleY($y + $ry)
        );
        $this->outf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c %s',
            $this->scale($x + $lx),
            $this->scaleY($y + $ry),
            $this->scale($x + $rx),
            $this->scaleY($y + $ly),
            $this->scale($x + $rx),
            $this->scaleY($y),
            $style->value
        );
    }
}
