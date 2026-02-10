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

use fpdf\Enums\PdfColorSpace;

/**
 * Contains information about an image.
 *
 * @internal
 */
final class PdfImage extends AbstractPdfNumber
{
    /**
     * @param int           $width            the image width
     * @param int           $height           the image height
     * @param PdfColorSpace $colorSpace       the color space
     * @param int           $bitsPerComponent the bits per component
     * @param string        $data             the image data
     * @param string        $filter           the image filter
     * @param ?string       $decodeParms      the optional decoded parameters
     * @param ?string       $softMask         the optional soft mask
     * @param string        $palette          the color palette
     * @param int[]|null    $transparencies   the optional transparencies
     * @param int           $index            the image index
     */
    public function __construct(
        public readonly int $width,
        public readonly int $height,
        public readonly PdfColorSpace $colorSpace,
        public readonly int $bitsPerComponent,
        public string $data,
        public readonly string $filter,
        public readonly ?string $decodeParms = null,
        public ?string $softMask = null,
        public readonly string $palette = '',
        public readonly ?array $transparencies = null,
        public int $index = 0,
    ) {}

    public function getColorSpaceValue(): string
    {
        return $this->colorSpace->value;
    }

    /**
     * @phpstan-assert-if-true non-empty-string $this->decodeParms
     */
    public function isDecodeParms(): bool
    {
        return $this->isString($this->decodeParms);
    }

    /**
     * @phpstan-assert-if-true non-empty-string $this->softMask
     */
    public function isSoftMask(): bool
    {
        return $this->isString($this->softMask);
    }

    /**
     * @phpstan-assert-if-true non-empty-array<int> $this->transparencies
     */
    public function isTransparencies(): bool
    {
        return null !== $this->transparencies && [] !== $this->transparencies;
    }
}
