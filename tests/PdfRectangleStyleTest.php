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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PdfRectangleStyle::class)]
class PdfRectangleStyleTest extends TestCase
{
    public function testValue(): void
    {
        self::assertSame('S', PdfRectangleStyle::BORDER->value);
        self::assertSame('B', PdfRectangleStyle::BOTH->value);
        self::assertSame('f', PdfRectangleStyle::FILL->value);
    }
}
