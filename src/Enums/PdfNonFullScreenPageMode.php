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

namespace fpdf\Enums;

use Elao\Enum\Attribute\EnumCase;
use fpdf\Interfaces\PdfEnumDefaultInterface;
use fpdf\Traits\PdfEnumDefaultTrait;

/**
 * The PDF exiting non-full-screen mode enumeration.
 *
 * Specifying how to display the document on exiting full-screen mode.
 *
 * This enumeration is meaningful only if the value of the <code>PageMode</code> entry in
 * the Catalog dictionary is <code>FullScreen</code>.
 *
 * @implements PdfEnumDefaultInterface<PdfNonFullScreenPageMode>
 */
enum PdfNonFullScreenPageMode: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /** Neither document outline nor thumbnail images should be visible (default value). */
    #[EnumCase(extras: [self::NAME => true])]
    case USE_NONE = 'UseNone';
    /** The optional content group panel should be visible. */
    case USE_OC = 'UseOC';
    /** The document outline should be visible. */
    case USE_OUTLINES = 'UseOutlines';
    /** The thumbnail images should be visible. */
    case USE_THUMBS = 'UseThumbs';
}
