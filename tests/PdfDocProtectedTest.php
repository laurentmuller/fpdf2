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

use fpdf\Enums\PdfOrientation;
use fpdf\Enums\PdfPageSize;
use fpdf\Enums\PdfUnit;
use fpdf\PdfBorder;
use fpdf\PdfDocument;
use fpdf\PdfException;
use fpdf\PdfSize;
use PHPUnit\Framework\TestCase;

class PdfDocProtectedTest extends TestCase
{
    public function testFormatBorder(): void
    {
        $doc = new class() extends PdfDocument {
            public function __construct(
                PdfOrientation $orientation = PdfOrientation::PORTRAIT,
                PdfUnit $unit = PdfUnit::MILLIMETER,
                PdfSize|PdfPageSize $size = PdfPageSize::A4
            ) {
                parent::__construct($orientation, $unit, $size);
                $this->formatBorders(0, 0, 0, 0, PdfBorder::none());
            }
        };
        $doc->addPage();
        self::assertSame(1, $doc->getPage());
    }

    public function testOutClosed(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid call: Document closed.');
        $doc = new class() extends PdfDocument {
            public function __construct(
                PdfOrientation $orientation = PdfOrientation::PORTRAIT,
                PdfUnit $unit = PdfUnit::MILLIMETER,
                PdfSize|PdfPageSize $size = PdfPageSize::A4
            ) {
                parent::__construct($orientation, $unit, $size);
                $this->close();
                $this->out('fake');
            }
        };
        $doc->addPage();
    }

    public function testOutEndPage(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid call: End page.');
        $doc = new class() extends PdfDocument {
            public function __construct(
                PdfOrientation $orientation = PdfOrientation::PORTRAIT,
                PdfUnit $unit = PdfUnit::MILLIMETER,
                PdfSize|PdfPageSize $size = PdfPageSize::A4
            ) {
                parent::__construct($orientation, $unit, $size);
                $this->endPage();
                $this->out('fake');
            }
        };
        $doc->addPage();
    }

    public function testOutNoPage(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid call: No page added.');
        $doc = new class() extends PdfDocument {
            public function __construct(
                PdfOrientation $orientation = PdfOrientation::PORTRAIT,
                PdfUnit $unit = PdfUnit::MILLIMETER,
                PdfSize|PdfPageSize $size = PdfPageSize::A4
            ) {
                parent::__construct($orientation, $unit, $size);
                $this->out('fake');
            }
        };
        $doc->addPage();
    }

    public function testUTF8(): void
    {
        $doc = new class() extends PdfDocument {
            public function __construct(
                PdfOrientation $orientation = PdfOrientation::PORTRAIT,
                PdfUnit $unit = PdfUnit::MILLIMETER,
                PdfSize|PdfPageSize $size = PdfPageSize::A4
            ) {
                parent::__construct($orientation, $unit, $size);
                if ($this->isUTF8('fake')) {
                    $this->pageSize->height = $this->getPageHeight();
                }
            }
        };
        $doc->addPage();
        self::assertSame(1, $doc->getPage());
    }
}
