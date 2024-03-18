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

namespace fpdf;

/**
 * Class implementing this interface parse image.
 *
 * @phpstan-import-type ImageType from PdfDocument
 */
interface PdfImageParserInterface
{
    /**
     * Parse the given file.
     *
     * @param PdfDocument $parent the parent document
     * @param string      $file   the image file to parse
     *
     * @return array the parsed image
     *
     * @throws PdfException if the image can not be parsed
     *
     * @phpstan-return ImageType
     */
    public function parse(PdfDocument $parent, string $file): array;
}
