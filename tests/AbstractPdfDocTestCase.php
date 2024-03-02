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

use PHPUnit\Framework\TestCase;

abstract class AbstractPdfDocTestCase extends TestCase
{
    protected function createDocument(bool $addPage = true, bool $addFont = true): PdfDocument
    {
        $doc = new PdfDocument();
        if ($addPage) {
            $doc->addPage();
        }
        if ($addFont) {
            $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR);
        }

        return $doc;
    }
}
