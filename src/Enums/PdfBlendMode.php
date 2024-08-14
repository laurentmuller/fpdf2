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

use fpdf\Traits\PdfTransparencyTrait;

/**
 * The PDF transparency blend mode enumeration.
 *
 * @see PdfTransparencyTrait::setAlpha()
 */
enum PdfBlendMode: string
{
    case COLOR = 'Color';
    case COLOR_BURN = 'ColorBurn';
    case COLOR_DODGE = 'ColorDodge';
    case DARKEN = 'Darken';
    case DIFFERENCE = 'Difference';
    case EXCLUSION = 'Exclusion';
    case HARD_LIGHT = 'HardLight';
    case HUE = 'Hue';
    case LIGHTEN = 'Lighten';
    case LUMINOSITY = 'Luminosity';
    case MULTIPLY = 'Multiply';
    case NORMAL = 'Normal';
    case OVERLAY = 'Overlay';
    case SATURATION = 'Saturation';
    case SCREEN = 'Screen';
    case SOFT_LIGHT = 'SoftLight';
}
