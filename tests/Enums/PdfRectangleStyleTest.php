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

namespace fpdf\Tests\Enums;

use fpdf\Enums\PdfRectangleStyle;
use PHPUnit\Framework\TestCase;

final class PdfRectangleStyleTest extends TestCase
{
    public function testValue(): void
    {
        self::assertSame('S', PdfRectangleStyle::BORDER->value);
        self::assertSame('B', PdfRectangleStyle::BOTH->value);
        self::assertSame('f', PdfRectangleStyle::FILL->value);
    }
}
