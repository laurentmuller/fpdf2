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
use fpdf\PdfRectangle;

/**
 * Trait to perform a rotation around a given center.
 *
 * The rotation affects all elements, which are printed after the method call (except clickable areas). Rotation is not
 * kept from page to page. Each page begins with no rotation.
 *
 * Only the display is altered. The getX() and getY() methods are not affected,
 * nor the automatic page break mechanism.
 *
 * All angle parameters are expressed in degrees.
 *
 * The code is inspired from FPDF script
 * <a href="http://www.fpdf.org/en/script/script2.php" target="_blank">Rotations</a>.
 *
 * @phpstan-require-extends PdfDocument
 */
trait PdfRotationTrait
{
    /**
     * The current rotation, in degrees.
     */
    private float $angle = 0.0;

    /**
     * Reset the rotation angle to 0.0.
     */
    public function endRotate(): void
    {
        if (!$this->isAngle($this->angle)) {
            return;
        }
        $this->out('Q');
        $this->angle = 0.0;
    }

    /**
     * Set the rotation angle.
     *
     * @param float      $angle the rotation angle, in degrees or 0.0 to stop rotation
     * @param float|null $x     the abscissa position or <code>null</code> to use the current abscissa
     * @param float|null $y     the ordinate position or <code>null</code> to use the current ordinate
     */
    public function rotate(float $angle, ?float $x = null, ?float $y = null): void
    {
        $this->endRotate();
        $angle = \fmod($angle, 360.0);
        if (!$this->isAngle($angle)) {
            return;
        }
        $this->angle = $angle;
        $x ??= $this->getX();
        $y ??= $this->getY();
        $angle *= \M_PI / 180.0;
        $cos = \cos($angle);
        $sin = \sin($angle);
        $cx = $this->scale($x);
        $cy = $this->scale($this->getPageHeight() - $y);
        $this->outf(
            'q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',
            $cos,
            $sin,
            -$sin,
            $cos,
            $cx,
            $cy,
            -$cx,
            -$cy
        );
    }

    /**
     * Rotate the given rectangle and end rotation.
     *
     * It can be drawn (border only), filled (with no border) or both. Do nothing if the angle is equal to 0.0.
     *
     * @param float             $x      the abscissa of the upper-left corner
     * @param float             $y      the ordinate of the upper-left corner
     * @param float             $width  the width
     * @param float             $height the height
     * @param float             $angle  the rotation angle, in degrees
     * @param PdfRectangleStyle $style  the border and fill style
     */
    public function rotateRect(
        float $x,
        float $y,
        float $width,
        float $height,
        float $angle,
        PdfRectangleStyle $style = PdfRectangleStyle::BORDER
    ): void {
        if (!$this->isAngle($angle)) {
            return;
        }
        $this->rotate($angle, $x, $y);
        $this->rect($x, $y, $width, $height, $style);
        $this->endRotate();
    }

    /**
     * Rotate the given rectangle and end rotation.
     *
     * It can be drawn (border only), filled (with no border) or both. Do nothing if the angle is equal to 0.0.
     *
     * @param PdfRectangle      $rectangle the rectangle to rotate
     * @param float             $angle     the rotation angle, in degrees
     * @param PdfRectangleStyle $style     the border and fill style
     */
    public function rotateRectangle(
        PdfRectangle $rectangle,
        float $angle,
        PdfRectangleStyle $style = PdfRectangleStyle::BORDER
    ): void {
        $this->rotateRect(
            $rectangle->x,
            $rectangle->y,
            $rectangle->width,
            $rectangle->height,
            $angle,
            $style
        );
    }

    /**
     * Rotate the given text and end rotation.
     *
     * Do nothing if the text is empty or if the angle is equal to 0.0.
     *
     * @param string     $text  the text to rotate
     * @param float      $angle the rotation angle, in degrees
     * @param float|null $x     the abscissa position or <code>null</code> to use the current abscissa
     * @param float|null $y     the ordinate position or <code>null</code> to use the current ordinate
     */
    public function rotateText(string $text, float $angle, ?float $x = null, ?float $y = null): void
    {
        if ('' === $text || !$this->isAngle($angle)) {
            return;
        }
        $x ??= $this->getX();
        $y ??= $this->getY();
        $this->rotate($angle, $x, $y);
        $this->text($x, $y, $text);
        $this->endRotate();
    }

    protected function endPage(): void
    {
        $this->endRotate();
        parent::endPage();
    }

    private function isAngle(float $angle): bool
    {
        return 0.0 !== \round($angle, 2);
    }
}
