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

namespace fpdf\Tests;

use fpdf\Color\PdfRgbColor;
use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\PdfDocument;
use PHPUnit\Framework\TestCase;

class PdfDocHeaderFooterTest extends TestCase
{
    public function testHeader(): void
    {
        $doc = $this->getDocument();
        $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR, 9.0);
        $doc->addPage();
        $doc->cell(text: 'Content 1');
        $doc->addPage();
        $doc->cell(text: 'Content 2');
        $doc->close();
        self::assertSame(2, $doc->getPage());
    }

    private function getDocument(): PdfDocument
    {
        return new class extends PdfDocument {
            #[\Override]
            public function header(): void
            {
                $this->setFont(PdfFontName::TIMES, PdfFontStyle::UNDERLINE, 12.0);
                $this->setDrawColor(PdfRgbColor::instance(10, 10, 10));
                $this->setTextColor(PdfRgbColor::instance(20, 20, 20));
                $this->setFillColor(PdfRgbColor::instance(30, 30, 30));
                $this->setLineWidth(2.0);
                $this->cell(text: 'Header');
            }

            #[\Override]
            public function footer(): void
            {
                $this->setFont(PdfFontName::TIMES, PdfFontStyle::BOLD, 10.0);
                $this->setDrawColor(PdfRgbColor::white());
                $this->setTextColor(PdfRgbColor::instance(100, 100, 100));
                $this->setFillColor(PdfRgbColor::instance(50, 50, 50));
                $this->setLineWidth(3.0);
                $this->cell(text: 'Footer');
            }
        };
    }
}
