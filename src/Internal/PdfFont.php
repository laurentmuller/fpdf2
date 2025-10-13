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

/**
 * Contains information about a font.
 *
 * @internal
 */
final class PdfFont extends AbstractPdfNumber
{
    /**
     * The font index.
     */
    public int $index = 0;

    /**
     * @param non-empty-string            $name         the font name
     * @param string                      $type         the font type
     * @param ?string                     $file         the optional font file path
     * @param ?string                     $encoding     the optional font encoding
     * @param array<int, int>             $cw           the character widths
     * @param int                         $up           the upper underline position
     * @param int                         $ut           the utter underline position
     * @param array<int, int|int[]>|null  $uv           the optional Unicode mapping
     * @param array<string, string|int[]> $desc         the font descriptions
     * @param ?string                     $diff         the optional font difference
     * @param int                         $length1      the first length
     * @param int                         $length2      the second length
     * @param int                         $originalsize the original size
     * @param int                         $size1        the first size
     * @param int                         $size2        the second size
     * @param bool                        $subsetted    the sub-setting characters state
     */
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public ?string $file,
        public readonly ?string $encoding,
        public readonly array $cw,
        public readonly int $up,
        public readonly int $ut,
        public readonly ?array $uv,
        public readonly array $desc,
        public readonly ?string $diff,
        public readonly int $length1,
        public readonly int $length2,
        public readonly int $originalsize,
        public readonly int $size1,
        public readonly int $size2,
        public readonly bool $subsetted,
    ) {}

    /**
     * @phpstan-assert-if-true non-empty-string $this->diff
     */
    public function isDiff(): bool
    {
        return $this->isString($this->diff);
    }

    /**
     * @phpstan-assert-if-true non-empty-string $this->encoding
     */
    public function isEncoding(): bool
    {
        return $this->isString($this->encoding);
    }

    /**
     * @phpstan-assert-if-true non-empty-string $this->file
     */
    public function isFile(): bool
    {
        return $this->isString($this->file);
    }

    /**
     * @phpstan-assert-if-true non-empty-array<int, int|int[]> $this->uv
     */
    public function isUv(): bool
    {
        return null !== $this->uv && [] !== $this->uv;
    }
}
