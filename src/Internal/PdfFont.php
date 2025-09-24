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
     * @var array<int, int> the character widths
     */
    public array $cw = [];
    /**
     * @var array<string, string|int[]> the font descriptions
     */
    public array $desc = [];
    /**
     * The optional font difference.
     */
    public ?string $diff = null;
    /**
     * The optional font encoding.
     */
    public ?string $encoding = null;
    /**
     * The optional font file path.
     */
    public ?string $file = null;
    /**
     * The font index.
     */
    public int $index = 0;
    /**
     * The first length.
     */
    public int $length1 = 0;
    /**
     * The second length.
     */
    public int $length2 = 0;
    /**
     * The original size.
     */
    public int $originalsize = 0;
    /**
     * The first size.
     */
    public int $size1 = 0;
    /**
     * The second size.
     */
    public int $size2 = 0;
    /**
     * The sub-setting characters state.
     */
    public bool $subsetted = false;
    /**
     * The font type.
     */
    public string $type = '';
    /**
     * The upper underline position.
     */
    public int $up = 0;
    /**
     * The utter underline position.
     */
    public int $ut = 0;
    /**
     * @var array<int, int|int[]>|null the optional Unicode mapping
     */
    public ?array $uv = null;

    /**
     * @param string $name the font name
     */
    public function __construct(public readonly string $name)
    {
    }

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
