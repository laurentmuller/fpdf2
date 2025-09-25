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

use fpdf\Enums\PdfBlendMode;

/**
 * Contains information about the transparency.
 *
 * @internal
 */
final class PdfTransparency extends AbstractPdfNumber
{
    /**
     * @param int          $index     the transparency index
     * @param float        $alpha     the alpha channel from 0.0 (fully transparent) to 1.0 (fully opaque)
     * @param PdfBlendMode $blendMode the blend mode
     */
    public function __construct(
        public readonly int $index,
        public readonly float $alpha,
        public readonly PdfBlendMode $blendMode = PdfBlendMode::NORMAL,
    ) {
    }

    public function getBlendModeValue(): string
    {
        return $this->blendMode->value;
    }
}
