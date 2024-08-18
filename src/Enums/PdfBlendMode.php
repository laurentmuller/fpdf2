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
    /**
     * Creates a color with the hue and saturation of the source color and the luminosity of the backdrop color.
     * This preserves the gray levels of the backdrop and is useful for coloring monochrome images or
     * tinting color images.
     */
    case COLOR = 'Color';
    /**
     * The annotation color is reflected by darkening the color of the background content.
     */
    case COLOR_BURN = 'ColorBurn';
    /**
     * The annotation color is reflected by brightening the color of the background content.
     */
    case COLOR_DODGE = 'ColorDodge';
    /**
     * Replaces only the areas that are lighter than the blend color. Areas darker than the blend color don’t change.
     */
    case DARKEN = 'Darken';
    /**
     * The darker color of the background content color and the annotation color is subtracted from the lighter color.
     */
    case DIFFERENCE = 'Difference';
    /**
     * This is the same effect as difference but with low contrast.
     */
    case EXCLUSION = 'Exclusion';
    /**
     * Multiplies or screens the colors, depending on the background content color. The effect is similar to that of
     * shining a harsh spotlight on the background content.
     */
    case HARD_LIGHT = 'HardLight';
    /**
     * Creates a color with the hue of the source color and the saturation and luminosity of the backdrop color.
     */
    case HUE = 'Hue';
    /**
     * Replaces only pixels that are darker than the blend color. Areas lighter than the blend color don’t change.
     */
    case LIGHTEN = 'Lighten';
    /**
     * Creates a color with the luminosity of the source color and the hue and saturation of the backdrop color.
     * This produces an inverse effect on that of the color mode.
     */
    case LUMINOSITY = 'Luminosity';
    /**
     * Multiplies the base color by the blend color, resulting in darker colors.
     */
    case MULTIPLY = 'Multiply';
    /**
     * Applies color normally, with no interaction with the base colors.
     */
    case NORMAL = 'Normal';
    /**
     * Multiplies or screens the colors, depending on the base colors.
     */
    case OVERLAY = 'Overlay';
    /**
     * Creates a color with the saturation of the source color and the hue and luminosity of the backdrop color.
     * Painting with this mode in an area of the backdrop that is a pure gray (no saturation) produces no change.
     */
    case SATURATION = 'Saturation';
    /**
     * Multiplies the inverse of the blend color by the base color, resulting in a bleaching effect.
     */
    case SCREEN = 'Screen';
    /**
     * Darkening or lightening of colors is based on the background content color. The effect is similar to that of shin.
     */
    case SOFT_LIGHT = 'SoftLight';
}
