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

namespace fpdf\Internal;

use fpdf\Enums\PdfRotation;
use fpdf\PdfSize;

/**
 * Contains information about a page.
 *
 * @internal
 */
final class PdfPageInfo extends AbstractPdfNumber
{
    /**
     * @param ?PdfRotation $rotation the optional page rotation
     * @param ?PdfSize     $size     the optional page size
     */
    public function __construct(
        public ?PdfRotation $rotation = null,
        public ?PdfSize $size = null,
    ) {}

    /**
     * @phpstan-assert-if-true PdfRotation $this->rotation
     */
    public function isRotation(): bool
    {
        return $this->rotation instanceof PdfRotation;
    }

    /**
     * @phpstan-assert-if-true PdfSize $this->size
     */
    public function isSize(): bool
    {
        return $this->size instanceof PdfSize;
    }
}
