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

namespace fpdf\Interfaces;

use fpdf\Internal\PdfImage;
use fpdf\PdfDocument;
use fpdf\PdfException;

/**
 * Class implementing this interface parse image.
 */
interface PdfImageParserInterface
{
    /**
     * Parse the given file.
     *
     * @param PdfDocument $parent the parent document
     * @param string      $file   the image file to parse
     *
     * @return PdfImage the parsed image
     *
     * @throws PdfException if the image cannot be parsed
     */
    public function parse(PdfDocument $parent, string $file): PdfImage;
}
