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

use fpdf\Enums\PdfLineCap;
use PHPUnit\Framework\TestCase;

class PdfLineCapTest extends TestCase
{
    public function testDefault(): void
    {
        self::assertSame(PdfLineCap::SQUARE, PdfLineCap::getDefault());
    }

    public function testValue(): void
    {
        self::assertSame(0, PdfLineCap::BUTT->value);
        self::assertSame(1, PdfLineCap::ROUND->value);
        self::assertSame(2, PdfLineCap::SQUARE->value);
    }
}
