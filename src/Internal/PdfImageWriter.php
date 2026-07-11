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

use fpdf\Enums\PdfColorSpace;
use fpdf\PdfWriter;

/**
 * Class to write images.
 */
readonly class PdfImageWriter
{
    public function __construct(private PdfWriter $writer)
    {
    }

    /**
     * Put an image to this writer.
     */
    public function putImage(PdfImage $image): void
    {
        $this->writer->putNewObj();
        $image->number = $this->writer->getObjectNumber();
        $this->writer->put('<</Type /XObject');
        $this->writer->put('/Subtype /Image');
        $this->writer->putf('/Width %d', $image->width);
        $this->writer->putf('/Height %d', $image->height);
        if ($image->isIndexed()) {
            $this->writer->putf(
                '/ColorSpace [/Indexed /DeviceRGB %d %d 0 R]',
                \intdiv(\strlen($image->palette), 3) - 1,
                $this->writer->getObjectNumber() + 1
            );
        } else {
            $this->writer->putf('/ColorSpace /%s', $image->colorSpace);
            if ($image->isDeviceCmyk()) {
                $this->writer->put('/Decode [1 0 1 0 1 0 1 0]');
            }
        }
        $this->writer->putf('/BitsPerComponent %d', $image->bitsPerComponent);
        $this->writer->putf('/Filter /%s', $image->filter);
        if ($image->isDecodeParms()) {
            $this->writer->putf('/DecodeParms <<%s>>', $image->decodeParms);
        }
        if ($image->isTransparencies()) {
            $transparencies = \array_map(
                static fn (int $value): string => PdfWriter::sprintf('%1$d %1$d', $value),
                $image->transparencies
            );
            $this->writer->putf('/Mask [%s]', \implode(' ', $transparencies));
        }
        if ($image->isSoftMask()) {
            $this->writer->putf('/SMask %d 0 R', $this->writer->getObjectNumber() + 1);
        }
        $this->writer->putf('/Length %d>>', \strlen($image->data));
        $this->writer->putStream($image->data);
        $this->writer->putEndObj();

        // soft mask
        if ($image->isSoftMask()) {
            $this->putSofMaskImage($image);
        }

        // palette
        if ($image->isIndexed()) {
            $this->writer->putStreamObject($image->palette);
        }

        // clear
        $image->clear();
    }

    /**
     * @param array<string, PdfImage> $images
     */
    public function putResourceImages(array $images): void
    {
        $this->writer->put('/XObject <<');
        foreach ($images as $image) {
            $this->writer->putf('/I%d %s', $image->index, $image->formatNumber());
        }
        $this->writer->put('>>');
    }

    private function putSofMaskImage(PdfImage $image): void
    {
        $decodeParms = PdfWriter::sprintf('/Predictor 15 /Colors 1 /BitsPerComponent 8 /Columns %d', $image->width);
        $softImage = new PdfImage(
            width: $image->width,
            height: $image->height,
            colorSpace: PdfColorSpace::DEVICE_GRAY,
            bitsPerComponent: 8,
            data: (string) $image->softMask,
            filter: $image->filter,
            decodeParms: $decodeParms
        );
        $this->putImage($softImage);
    }
}
