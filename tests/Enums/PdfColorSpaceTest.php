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

use fpdf\Enums\PdfColorSpace;
use PHPUnit\Framework\TestCase;

final class PdfColorSpaceTest extends TestCase
{
    public function testColors(): void
    {
        self::assertSame(1, PdfColorSpace::DEVICE_CMYK->getColors());
        self::assertSame(1, PdfColorSpace::DEVICE_GRAY->getColors());
        self::assertSame(3, PdfColorSpace::DEVICE_RGB->getColors());
        self::assertSame(1, PdfColorSpace::INDEXED->getColors());
    }
}
