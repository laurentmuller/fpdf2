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
    ) {
    }
}
