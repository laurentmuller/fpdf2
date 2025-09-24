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

use fpdf\PdfRectangle;

/**
 * Contains information about a page link.
 *
 * @internal
 */
final class PdfPageLink extends AbstractPdfNumber
{
    /**
     * @param PdfRectangle $rect the rectangular area of the link
     * @param string|int   $link a URL or an identifier returned by <code>addLink()</code>
     */
    public function __construct(
        public readonly PdfRectangle $rect,
        public readonly string|int $link
    ) {
    }

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
}
