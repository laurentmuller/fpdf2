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

namespace fpdf\ImageParsers;

use fpdf\Enums\PdfColorSpace;
use fpdf\Interfaces\PdfImageParserInterface;
use fpdf\Internal\PdfImage;
use fpdf\PdfDocument;
use fpdf\PdfException;

/**
 * Parser for JPEG images.
 */
class PdfJpgParser implements PdfImageParserInterface
{
    #[\Override]
    public function parse(PdfDocument $parent, string $file): PdfImage
    {
        /* @var array{0: int, 1: int, 2: int, channels?: int, bits?: int}|false $size */
        $size = \getimagesize($file);
        if (!\is_array($size)) {
            throw PdfException::format('Missing or invalid image size: %s.', $file);
        }

        if (\IMG_JPG !== $size[2]) {
            throw PdfException::format('Invalid JPEG image type (%d): %s.', $size[2], $file);
        }

        $channels = $size['channels'] ?? 3;
        $colorSpace = match ($channels) {
            3 => PdfColorSpace::DEVICE_RGB,
            4 => PdfColorSpace::DEVICE_CMYK,
            default => PdfColorSpace::DEVICE_GRAY
        };
        $bitsPerComponent = $size['bits'] ?? 8;
        $data = (string) \file_get_contents($file);

        return new PdfImage(
            width: $size[0],
            height: $size[1],
            colorSpace: $colorSpace,
            bitsPerComponent: $bitsPerComponent,
            data: $data,
            filter: 'DCTDecode'
        );
    }
}
