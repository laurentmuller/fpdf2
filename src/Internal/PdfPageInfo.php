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
