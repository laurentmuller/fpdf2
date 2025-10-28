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

use fpdf\Enums\PdfPageSize;
use fpdf\Enums\PdfUnit;
use PHPUnit\Framework\TestCase;

final class PdfPageSizeTest extends TestCase
{
    public function testA4(): void
    {
        $size = PdfPageSize::A4;
        $width = $size->getWidth();
        self::assertSame(210.0, $width);
        $height = $size->getHeight();
        self::assertSame(297.0, $height);
        $unit = $size->getUnit();
        self::assertSame(PdfUnit::MILLIMETER, $unit);
    }

    public function testAll(): void
    {
        $pageSizes = PdfPageSize::cases();
        foreach ($pageSizes as $pageSize) {
            self::assertGreaterThan(0, $pageSize->getWidth());
            self::assertGreaterThan(0, $pageSize->getHeight());
            $size = $pageSize->getSize();
            self::assertGreaterThan(0, $size->width);
            self::assertGreaterThan(0, $size->height);
        }
    }
}
