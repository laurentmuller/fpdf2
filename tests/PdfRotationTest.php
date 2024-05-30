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

#[CoversClass(PdfRotation::class)]
class PdfRotationTest extends TestCase
{
    public function testValues(): void
    {
        self::assertSame(0, PdfRotation::DEFAULT->value);
        self::assertSame(90, PdfRotation::CLOCKWISE_90->value);
        self::assertSame(180, PdfRotation::CLOCKWISE_180->value);
        self::assertSame(270, PdfRotation::CLOCKWISE_270->value);
    }
}
