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

use fpdf\PdfDocument;
use fpdf\PdfRectangle;

/**
 * Trait to draw dash lines.
 *
 * The code is inspired from FPDF script
 * <a href="https://www.fpdf.org/en/script/script33.php" target="_blank">Dashes</a>.
 *
 * @phpstan-require-extends PdfDocument
 */
trait PdfDashTrait
{
    /**
     * Draw a dashed rectangle.
     *
     * After this call, the dashed pattern and the line width are restored.
     *
     * @param float  $x         the abscissa of the upper-left corner
     * @param float  $y         the ordinate of the upper-left corner
     * @param float  $w         the width
     * @param float  $h         the height
     * @param float  $dashes    the length of dashes and gaps
     * @param ?float $lineWidth the line width or <code>null</code> to use the current line width
     */
    public function dashedRect(
        float $x,
        float $y,
        float $w,
        float $h,
        float $dashes,
        ?float $lineWidth = null
    ): void {
        $oldWidth = $this->lineWidth;
        if (\is_float($lineWidth)) {
            $this->setLineWidth($lineWidth);
        }

        $this->setDashPattern($dashes, $dashes);
        $this->rect($x, $y, $w, $h);
        $this->resetDashPattern();

        if (\is_float($lineWidth)) {
            $this->setLineWidth($oldWidth);
        }
    }

    /**
     * Draw a dashed rectangle.
     *
     * After this call, the dashed pattern and the line width are restored.
     *
     * @param PdfRectangle $rectangle the rectangle to draw
     * @param float        $dashes    the length of dashes and gaps
     * @param ?float       $lineWidth the line width or <code>null</code> to use the current line width
     */
    public function dashedRectangle(PdfRectangle $rectangle, float $dashes, ?float $lineWidth = null): void
    {
        $this->dashedRect(
            $rectangle->x,
            $rectangle->y,
            $rectangle->width,
            $rectangle->height,
            $dashes,
            $lineWidth
        );
    }

    /**
     * Restore normal drawing.
     */
    public function resetDashPattern(): void
    {
        $this->writer->out($this->page, '[] 0 d');
    }

    /**
     * Sets the dash line.
     *
     * @param float $dashes the length of dashes
     * @param float $gaps   the length of gaps
     */
    public function setDashPattern(float $dashes, float $gaps): void
    {
        $this->writer->outf($this->page, '[%.3F %.3F] 0 d', $this->scale($dashes), $this->scale($gaps));
    }
}
