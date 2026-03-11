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

use fpdf\Enums\PdfDirection;
use PHPUnit\Framework\TestCase;

final class PdfDirectionTest extends TestCase
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
