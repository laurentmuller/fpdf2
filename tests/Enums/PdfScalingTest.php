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

class PdfScalingTest extends TestCase
{
    public function testDefault(): void
    {
        self::assertSame(PdfScaling::APP_DEFAULT, PdfScaling::getDefault());
    }

    public function testValue(): void
    {
        self::assertSame('AppDefault', PdfScaling::APP_DEFAULT->value);
        self::assertSame('None', PdfScaling::NONE->value);
    }
}
