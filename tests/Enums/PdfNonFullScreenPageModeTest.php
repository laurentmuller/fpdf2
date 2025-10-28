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

use fpdf\Enums\PdfNonFullScreenPageMode;
use PHPUnit\Framework\TestCase;

final class PdfNonFullScreenPageModeTest extends TestCase
{
    public function testDefault(): void
    {
        self::assertSame(PdfNonFullScreenPageMode::USE_NONE, PdfNonFullScreenPageMode::getDefault());
    }

    public function testValue(): void
    {
        self::assertSame('UseNone', PdfNonFullScreenPageMode::USE_NONE->value);
        self::assertSame('UseOC', PdfNonFullScreenPageMode::USE_OC->value);
        self::assertSame('UseOutlines', PdfNonFullScreenPageMode::USE_OUTLINES->value);
        self::assertSame('UseThumbs', PdfNonFullScreenPageMode::USE_THUMBS->value);
    }
}
