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

use fpdf\Interfaces\PdfImageParserInterface;
use fpdf\PdfDocument;
use fpdf\PdfException;

/**
 * Parser for JPEG images.
 *
 * @phpstan-import-type ImageType from PdfDocument
 */
class PdfJpgParser implements PdfImageParserInterface
{
    /**
     * @phpstan-return ImageType
     */
    public function parse(PdfDocument $parent, string $file): array
    {
        /* @phpstan-var array{0: int, 1: int, 2: int, channels?: int, bits?: int}|false $size */
        $size = \getimagesize($file);
        if (!\is_array($size)) {
            throw PdfException::format('Missing or incorrect image file: %s.', $file);
        }

        if (\IMG_JPG !== $size[2]) {
            throw PdfException::format('The file is not a JPEG image: %s.', $file);
        }

        /** @phpstan-var int<3,5> $channels */
        $channels = $size['channels'] ?? 3;
        $color_space = match ($channels) {
            3 => 'DeviceRGB',
            4 => 'DeviceCMYK',
            default => 'DeviceGray'
        };
        $bitsPerComponent = $size['bits'] ?? 8;
        $data = (string) \file_get_contents($file);

        return [
            'index' => 0,
            'number' => 0,
            'width' => $size[0],
            'height' => $size[1],
            'color_space' => $color_space,
            'bits_per_component' => $bitsPerComponent,
            'filter' => 'DCTDecode',
            'data' => $data,
            'palette' => '',
        ];
    }
}
