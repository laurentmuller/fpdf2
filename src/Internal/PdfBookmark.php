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
 * Contains information about a bookmark.
 *
 * @internal
 */
final class PdfBookmark
{
    /** @var array<string, int> the bookmark hierarchy */
    public array $hierarchy = [];

    /**
     * @param string   $text  the text
     * @param int      $level the level
     * @param float    $y     the ordinate
     * @param int      $page  the page
     * @param int|null $link  the optional link
     *
     * @phpstan-param non-negative-int $level
     */
    public function __construct(
        public readonly string $text,
        public readonly int $level,
        public readonly float $y,
        public readonly int $page,
        public readonly ?int $link = null,
    ) {}
}
