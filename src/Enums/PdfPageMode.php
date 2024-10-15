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
 * The PDF page mode enumeration.
 *
 * Specifying how the document shall be displayed when opened.
 *
 * @implements PdfEnumDefaultInterface<PdfPageMode>
 */
enum PdfPageMode: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /**
     * The full-screen mode, with no menu bar, window controls, or any other window visible.
     */
    case FULL_SCREEN = 'FullScreen';

    /**
     * Attachments panel is visible.
     *
     * Require PDF 1.6.
     */
    case USE_ATTACHMENTS = 'UseAttachments';

    /**
     * Neither document outline nor thumbnail images visible. This is the default value.
     */
    #[EnumCase(extras: [self::NAME => true])]
    case USE_NONE = 'UseNone';

    /**
     * Optional content group panel visible.
     *
     * Require PDF 1.5.
     */
    case USE_OC = 'UseOC';

    /**
     * Document outline visible.
     */
    case USE_OUTLINES = 'UseOutlines';

    /**
     * Thumbnail images visible.
     */
    case USE_THUMBS = 'UseThumbs';

    /**
     * Gets the required PDF version.
     */
    public function getVersion(): PdfVersion
    {
        return match ($this) {
            self::USE_OC => PdfVersion::VERSION_1_5,
            self::USE_ATTACHMENTS => PdfVersion::VERSION_1_6,
            default => PdfVersion::getDefault()
        };
    }
}
