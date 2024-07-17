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

namespace fpdf;

use PHPUnit\Framework\TestCase;

class PdfUnitTest extends TestCase
{
    public function testScaleFactor(): void
    {
        self::assertEqualsWithDelta(28.34, PdfUnit::CENTIMETER->getScaleFactor(), 0.01);
        self::assertEqualsWithDelta(72.0, PdfUnit::INCH->getScaleFactor(), 0.01);
        self::assertEqualsWithDelta(2.83, PdfUnit::MILLIMETER->getScaleFactor(), 0.01);
        self::assertEqualsWithDelta(1.0, PdfUnit::POINT->getScaleFactor(), 0.01);
    }

    public function testValue(): void
    {
        self::assertSame('cm', PdfUnit::CENTIMETER->value);
        self::assertSame('in', PdfUnit::INCH->value);
        self::assertSame('mm', PdfUnit::MILLIMETER->value);
        self::assertSame('pt', PdfUnit::POINT->value);
    }
}
