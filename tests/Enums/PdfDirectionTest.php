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

use fpdf\Enums\PdfDirection;
use PHPUnit\Framework\TestCase;

class PdfDirectionTest extends TestCase
{
    public function testDefault(): void
    {
        self::assertSame(PdfDirection::L2R, PdfDirection::getDefault());
    }

    public function testValue(): void
    {
        self::assertSame('L2R', PdfDirection::L2R->value);
        self::assertSame('R2L', PdfDirection::R2L->value);
    }
}
