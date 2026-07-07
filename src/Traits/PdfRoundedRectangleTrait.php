<?php

/*
 * This file is part of the FPDF2 package.
 *
 * (c) bibi.nu <bibi@bibi.nu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace fpdf\Traits;

use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfRectangleStyle;
use fpdf\PdfDocument;
use fpdf\PdfException;
use fpdf\PdfRectangle;

/**
 * Trait to draw rounded rectangles.
 *
 * The code is inspired from FPDF script
 * <a href="https://www.fpdf.org/en/script/script7.php" target="_blank">Rounded rectangle</a>
 * created by Maxime Delorme.
 *
 * @phpstan-require-extends PdfDocument
 */
trait PdfRoundedRectangleTrait
{
    /**
     * Output a rounded rectangle.
     *
     * @param float             $width  the width of the rectangle
     * @param float             $height the height of the rectangle
     * @param float             $radius the radius of the corners, the maximum value is half the minimum of the width
     *                                  and height
     * @param ?float            $x      the abscissa of the rectangle or null to use the current abscissa
     * @param ?float            $y      the ordinate of the rectangle or null to use the current ordinate
     * @param PdfRectangleStyle $style  the style of rendering
     * @param PdfMove           $move   indicates where the current position should go after the call
     *
     * @throws PdfException if the radius is not positive
     */
    public function roundedRect(
        float $width,
        float $height,
        float $radius,
        ?float $x = null,
        ?float $y = null,
        PdfRectangleStyle $style = PdfRectangleStyle::BOTH,
        PdfMove $move = PdfMove::RIGHT
    ): static {
        // check radius
        if ($radius <= 0.0) {
            throw PdfException::format('The radius must be positive, %s given.', $radius);
        }

        $x ??= $this->x;
        $y ??= $this->y;
        $radius = \min($radius, $width / 2.0, $height / 2.0);
        $length = 4.0 / 3.0 * (\M_SQRT2 - 1.0) * $radius;

        // top-left point
        $this->outputPoint($x + $radius, $y, 'm');
        // top-right point
        $xc = $x + $width - $radius;
        $yc = $y + $radius;
        $this->outputPoint($xc, $y);
        // top-right arc
        $this->outputArc(
            $xc + $length,
            $yc - $radius,
            $xc + $radius,
            $yc - $length,
            $xc + $radius,
            $yc
        );
        // right-bottom point
        $yc = $y + $height - $radius;
        $this->outputPoint($x + $width, $yc);
        // right-bottom arc
        $this->outputArc(
            $xc + $radius,
            $yc + $length,
            $xc + $length,
            $yc + $radius,
            $xc,
            $yc + $radius
        );
        // left-bottom point
        $xc = $x + $radius;
        $this->outputPoint($xc, $y + $height);
        // left-bottom arc
        $this->outputArc(
            $xc - $length,
            $yc + $radius,
            $xc - $radius,
            $yc + $length,
            $xc - $radius,
            $yc
        );
        // left-top point
        $yc = $y + $radius;
        $this->outputPoint($x, $yc);
        // left-top arc
        $this->outputArc(
            $xc - $radius,
            $yc - $length,
            $xc - $length,
            $yc - $radius,
            $xc,
            $yc - $radius
        );

        // style
        $this->writer->out($this->page, $style->lowercase());

        switch ($move) {
            case PdfMove::RIGHT:
                $this->x = $x + $width;
                $this->y = $y;
                break;
            case PdfMove::NEW_LINE:
                $this->x = $this->margins->left;
                $this->y = $y + $height;
                break;
            case PdfMove::BELOW:
                $this->x = $x;
                $this->y = $y + $height;
                break;
        }

        return $this;
    }

    /**
     * Output a rounded rectangle.
     *
     * @param PdfRectangle      $rect   the rectangle to draw
     * @param float             $radius the radius of the corners, the maximum value is half the minimum of the width
     *                                  and height
     * @param PdfRectangleStyle $style  the style of rendering
     * @param PdfMove           $move   indicates where the current position should go after the call
     *
     * @throws PdfException if the radius is not positive
     */
    public function roundedRectangle(
        PdfRectangle $rect,
        float $radius,
        PdfRectangleStyle $style = PdfRectangleStyle::BOTH,
        PdfMove $move = PdfMove::RIGHT
    ): static {
        return $this->roundedRect(
            width: $rect->width,
            height: $rect->height,
            radius: $radius,
            x: $rect->x,
            y: $rect->y,
            style: $style,
            move: $move
        );
    }

    private function outputArc(float $x1, float $y1, float $x2, float $y2, float $x3, float $y3): void
    {
        $this->writer->outf(
            $this->page,
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            $this->scale($x1),
            $this->scaleY($y1),
            $this->scale($x2),
            $this->scaleY($y2),
            $this->scale($x3),
            $this->scaleY($y3)
        );
    }

    private function outputPoint(float $x, float $y, string $tag = 'l'): void
    {
        $this->writer->outf(
            $this->page,
            '%.2F %.2F ' . $tag,
            $this->scale($x),
            $this->scaleY($y)
        );
    }
}
