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
use fpdf\Internal\PdfTransparency;
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
 * @phpstan-require-extends PdfDocument
 */
trait PdfTransparencyTrait
{
    /**
     * The transparencies.
     *
     * @var PdfTransparency[]
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
        $index = \count($this->transparencies) + 1;
        $this->transparencies[] = new PdfTransparency(
            index: $index,
            alpha: \max(0.0, \min($alpha, 1.0)),
            blendMode: $blendMode,
        );
        $this->outf('/GS%d gs', $index);

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
        foreach ($this->transparencies as $transparency) {
            $this->putf('/GS%d %d 0 R', $transparency->index, $transparency->number);
        }
        $this->put('>>');
    }

    protected function putResources(): void
    {
        foreach ($this->transparencies as $transparency) {
            $this->putNewObj();
            $transparency->number = $this->objectNumber;
            $this->putf(
                '<</Type /ExtGState /ca %1$.3F /CA %1$.3F /BM /%2$s>>',
                $transparency->alpha,
                $transparency->getBlendModeValue()
            );
            $this->putEndObj();
        }
        parent::putResources();
    }
}
