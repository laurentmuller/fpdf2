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

namespace fpdf\Enums;

/**
 * The PDF image color space enumeration.
 */
enum PdfColorSpace: string
{
    case DEVICE_CMYK = 'DeviceCMYK';
    case DEVICE_GRAY = 'DeviceGray';
    case DEVICE_RGB = 'DeviceRGB';
    case INDEXED = 'Indexed';

    /**
     * Gets the number of colors.
     */
    public function getColors(): int
    {
        return match ($this) {
            self::DEVICE_RGB => 3,
            default => 1,
        };
    }
}
