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

namespace fpdf\Enums;

use PHPUnit\Framework\TestCase;

class PdfLayoutTest extends TestCase
{
    public function testDefault(): void
    {
        self::assertSame(PdfLayout::DEFAULT, PdfLayout::getDefault());
    }

    public function testValue(): void
    {
        self::assertSame('OneColumn', PdfLayout::CONTINUOUS->value);
        self::assertSame('', PdfLayout::DEFAULT->value);
        self::assertSame('SinglePage', PdfLayout::SINGLE->value);
        self::assertSame('TwoColumnLeft', PdfLayout::TWO_PAGES->value);
    }
}
