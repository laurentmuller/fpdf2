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

use fpdf\Enums\PdfRotation;
use PHPUnit\Framework\TestCase;

final class PdfRotationTest extends TestCase
{
    public function testValues(): void
    {
        self::assertSame(0, PdfRotation::DEFAULT->value);
        self::assertSame(90, PdfRotation::CLOCKWISE_90->value);
        self::assertSame(180, PdfRotation::CLOCKWISE_180->value);
        self::assertSame(270, PdfRotation::CLOCKWISE_270->value);
    }
}
