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

class PdfLineJoinTest extends TestCase
{
    public function testValue(): void
    {
        self::assertSame(2, PdfLineJoin::BEVEL->value);
        self::assertSame(0, PdfLineJoin::MITER->value);
        self::assertSame(1, PdfLineJoin::ROUND->value);
    }
}
