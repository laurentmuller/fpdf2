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

use fpdf\Enums\PdfAnnotationName;
use fpdf\Interfaces\PdfColorInterface;
use fpdf\PdfRectangle;

/**
 * Contains information about a page annotation.
 *
 * @internal
 */
final class PdfPageAnnotation extends AbstractPdfNumber
{
    /**
     * @param PdfRectangle       $rect  the rectangular area of the annotation
     * @param string             $text  the annotation text
     * @param ?string            $title the optional annotation title
     * @param PdfAnnotationName  $name  the annotation name (icon)
     * @param ?PdfColorInterface $color the optional annotation color or <code>null</code> for default (black)
     */
    public function __construct(
        public readonly PdfRectangle $rect,
        public readonly string $text,
        public ?string $title = null,
        public PdfAnnotationName $name = PdfAnnotationName::NOTE,
        public ?PdfColorInterface $color = null
    ) {}

    /**
     * Format this rectangular area.
     */
    public function formatRectangle(): string
    {
        return \sprintf(
            '%.2F %.2F %.2F %.2F',
            $this->rect->x,
            $this->rect->y,
            $this->rect->x + $this->rect->width,
            $this->rect->y - $this->rect->height
        );
    }

    public function getColorTag(): string
    {
        return $this->color?->getTag() ?? '';
    }

    public function getNameValue(): string
    {
        return $this->name->value;
    }

    /**
     * @phpstan-assert-if-true PdfColorInterface $this->color
     */
    public function isColor(): bool
    {
        return $this->color instanceof PdfColorInterface;
    }

    /**
     * @phpstan-assert-if-true non-empty-string $this->title
     */
    public function isTitle(): bool
    {
        return $this->isString($this->title);
    }
}
