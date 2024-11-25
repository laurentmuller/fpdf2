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

use fpdf\Enums\PdfBlendMode;
use fpdf\Enums\PdfVersion;
use fpdf\PdfDocument;

/**
 * Trait to add transparency support.
 *
 * The alpha channel can be from 0.0 (fully transparent) to 1.0 (fully opaque). It applies to all
 * elements (texts, drawings, images).
 *
 * The code is inspired from FPDF script
 * <a href="http://www.fpdf.org/en/script/script74.php" target="_blank">Transparency</a>.
 *
 * @phpstan-type PdfTransparencyType = array{
 *      key: int,
 *      object: int,
 *      alpha: float,
 *      blendMode: string}
 *
 * @phpstan-require-extends PdfDocument
 */
trait PdfTransparencyTrait
{
    /**
     * The transparencies.
     *
     * @phpstan-var PdfTransparencyType[]
     */
    private array $transparencies = [];

    /**
     * Reset the alpha mode to the default value (1.0).
     */
    public function resetAlpha(): static
    {
        return $this->setAlpha(1.0);
    }

    /**
     * Set the alpha mode.
     *
     * @param float        $alpha     the alpha channel from 0.0 (fully transparent) to 1.0 (fully opaque)
     * @param PdfBlendMode $blendMode the blend mode
     */
    public function setAlpha(float $alpha, PdfBlendMode $blendMode = PdfBlendMode::NORMAL): static
    {
        $key = \count($this->transparencies) + 1;
        $this->transparencies[] = [
            'key' => $key,
            'object' => 0,
            'alpha' => \max(0.0, \min($alpha, 1.0)),
            'blendMode' => $blendMode->value,
        ];
        $this->outf('/GS%d gs', $key);

        return $this;
    }

    protected function endDoc(): void
    {
        if ([] !== $this->transparencies) {
            $this->updatePdfVersion(PdfVersion::VERSION_1_4);
        }
        parent::endDoc();
    }

    protected function putResourceDictionary(): void
    {
        parent::putResourceDictionary();
        if ([] === $this->transparencies) {
            return;
        }
        $this->put('/ExtGState <<');
        foreach ($this->transparencies as $state) {
            $this->putf('/GS%d %d 0 R', $state['key'], $state['object']);
        }
        $this->put('>>');
    }

    protected function putResources(): void
    {
        foreach ($this->transparencies as &$state) {
            $this->putNewObj();
            $state['object'] = $this->objectNumber;
            $this->put('<</Type /ExtGState');
            $this->putf('/ca %.3F', $state['alpha']);
            $this->putf('/CA %.3F', $state['alpha']);
            $this->putf('/BM /%s', $state['blendMode']);
            $this->put('>>');
            $this->putEndObj();
        }
        parent::putResources();
    }
}
