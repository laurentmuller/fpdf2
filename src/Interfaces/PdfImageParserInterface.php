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
