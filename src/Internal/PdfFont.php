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
     * @param non-empty-string            $name         The font name.
     * @param string                      $type         The font type.
     * @param ?string                     $file         The optional font file path.
     * @param ?string                     $encoding     The optional font encoding.
     * @param array<int, int>             $cw           The character widths.
     * @param int                         $up           The upper underline position.
     * @param int                         $ut           The utter underline position.
     * @param array<int, int|int[]>|null  $uv           The optional Unicode mapping.
     * @param array<string, string|int[]> $desc         The font descriptions.
     * @param ?string                     $diff         The optional font difference.
     * @param int                         $length1      The first length.
     * @param int                         $length2      The second length.
     * @param int                         $originalsize The original size.
     * @param int                         $size1        The first size.
     * @param int                         $size2        The second size.
     * @param bool                        $subsetted    The sub-setting characters state.
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
