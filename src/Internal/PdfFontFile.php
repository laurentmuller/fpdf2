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
 * Contains information about a font file.
 *
 * @internal
 */
final class PdfFontFile extends AbstractPdfNumber
{
    /**
     * @param int  $length1 the first length
     * @param ?int $length2 the optional second length
     */
    public function __construct(
        public readonly int $length1,
        public readonly ?int $length2 = null,
    ) {
    }

    /**
     * @phpstan-assert-if-true int $this->length2
     */
    public function isLength2(): bool
    {
        return null !== $this->length2;
    }
}
