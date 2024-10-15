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

namespace fpdf\Tests\Enums;

use fpdf\Enums\PdfZoom;
use PHPUnit\Framework\TestCase;

class PdfZoomTest extends TestCase
{
    public function testDefault(): void
    {
        self::assertSame(PdfZoom::DEFAULT, PdfZoom::getDefault());
    }

    public function testValue(): void
    {
        self::assertSame('', PdfZoom::DEFAULT->value);
        self::assertSame('Fit', PdfZoom::FULL_PAGE->value);
        self::assertSame('FitH null', PdfZoom::FULL_WIDTH->value);
        self::assertSame('XYZ null null 1', PdfZoom::REAL->value);
    }
}
