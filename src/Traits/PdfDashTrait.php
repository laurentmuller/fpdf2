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

namespace fpdf\Traits;

use fpdf\PdfDocument;

/**
 * Trait to draw dash lines.
 *
 * The code is inspired from FPDF script
 * <a href="https://www.fpdf.org/en/script/script33.php" target="_blank">Dashes</a>.
 *
 * @phpstan-require-extends PdfDocument
 */
trait PdfDashTrait
{
    /**
     * Restore normal drawing.
     */
    public function resetDash(): void
    {
        $this->out('[] 0 d');
    }

    /**
     * Sets the dash line.
     *
     * @param float $dashes the length of dashes
     * @param float $gaps   the length of gaps
     */
    public function setDash(float $dashes, float $gaps): void
    {
        $this->outf('[%.3F %.3F] 0 d', $this->scale($dashes), $this->scale($gaps));
    }
}
