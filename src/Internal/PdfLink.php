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
 * Contains information about a link.
 *
 * @internal
 */
final readonly class PdfLink
{
    /**
     * @param int   $page the page number
     * @param float $y    the link ordinate position
     */
    public function __construct(
        public int $page = 0,
        public float $y = 0,
    ) {}
}
