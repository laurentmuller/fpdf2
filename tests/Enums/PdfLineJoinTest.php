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

use fpdf\Enums\PdfLineJoin;
use PHPUnit\Framework\TestCase;

final class PdfLineJoinTest extends TestCase
{
    public function testDefault(): void
    {
        self::assertSame(PdfLineJoin::MITER, PdfLineJoin::getDefault());
    }

    public function testValue(): void
    {
        self::assertSame(2, PdfLineJoin::BEVEL->value);
        self::assertSame(0, PdfLineJoin::MITER->value);
        self::assertSame(1, PdfLineJoin::ROUND->value);
    }
}
