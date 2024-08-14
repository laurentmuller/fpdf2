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

namespace fpdf\Traits;

use fpdf\Enums\PdfDestination;
use fpdf\Enums\PdfFontName;
use fpdf\PdfDocument;
use fpdf\PdfRectangle;
use PHPUnit\Framework\TestCase;

class PdfRotationTraitTest extends TestCase
{
    public function testRender(): void
    {
        $document = new class() extends PdfDocument {
            use PdfRotationTrait;
        };
        $document->addPage();
        $document->setFont(PdfFontName::ARIAL);

        $document->rotate(0);
        $document->rotate(45);
        $document->endRotate();

        $document->rotateRect(10, 10, 100, 50, 0);
        $document->rotateRect(10, 10, 100, 50, 45);
        $document->endRotate();

        $document->rotateRectangle(PdfRectangle::instance(10, 10, 100, 50), 0);
        $document->rotateRectangle(PdfRectangle::instance(10, 10, 100, 50), 45);
        $document->endRotate();

        $document->rotateText('', 45);
        $document->rotateText('Text', 0);
        $document->rotateText('Text', 45);
        $document->endRotate();

        $document->output(PdfDestination::STRING);
        self::assertSame(1, $document->getPage());
    }
}
