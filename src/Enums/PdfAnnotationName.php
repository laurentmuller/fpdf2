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
 * The PDF annotation name enumeration.
 *
 * This is the name of an icon that shall be used in displaying annotations.
 *
 * @implements PdfEnumDefaultInterface<PdfAnnotationName>
 */
enum PdfAnnotationName: string implements PdfEnumDefaultInterface
{
    use PdfEnumDefaultTrait;

    /** The comment icon. */
    case COMMENT = 'Comment';
    /** The help icon. */
    case HELP = 'Help';
    /** The insert icon. */
    case INSERT = 'Insert';
    /** The key icon. */
    case KEY = 'Key';
    /** The new paragraph icon. */
    case NEW_PARAGRAPH = 'NewParagraph';
    /** The note icon (default value). */
    #[EnumCase(extras: [self::NAME => true])]
    case NOTE = 'Note';
    /** The paragraph icon. */
    case PARAGRAPH = 'Paragraph';
}
