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

use Elao\Enum\Attribute\EnumCase;
use fpdf\Interfaces\PdfEnumDefaultInterface;
use fpdf\Traits\PdfEnumDefaultTrait;

/**
 * Specifying how to display the document on exiting full-screen mode.
 *
 * @implements PdfEnumDefaultInterface<PdfNonFullScreenPageMode>
 */
enum PdfNonFullScreenPageMode: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /**
     * Neither document outline nor thumbnail images should be visible (default value).
     */
    #[EnumCase(extras: [self::NAME => true])]
    case USE_NONE = 'UseNone';
    /**
     * The optional content group panel should be visible.
     */
    case USE_OC = 'UseOC';
    /**
     * The document outline should be visible.
     */
    case USE_OUTLINES = 'UseOutlines';
    /**
     * The thumbnail images should be visible.
     */
    case USE_THUMBS = 'UseThumbs';
}
