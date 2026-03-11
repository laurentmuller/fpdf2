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

use fpdf\Enums\PdfScaling;
use PHPUnit\Framework\TestCase;

final class PdfScalingTest extends TestCase
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
