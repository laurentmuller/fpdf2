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

use fpdf\Enums\PdfDuplex;
use PHPUnit\Framework\TestCase;

final class PdfDuplexTest extends TestCase
{
    public function testDefault(): void
    {
        self::assertSame(PdfDuplex::NONE, PdfDuplex::getDefault());
    }

    public function testValue(): void
    {
        self::assertSame('DuplexFlipLongEdge', PdfDuplex::DUPLEX_FLIP_LONG_EDGE->value);
        self::assertSame('DuplexFlipShortEdge', PdfDuplex::DUPLEX_FLIP_SHORT_EDGE->value);
        self::assertSame('None', PdfDuplex::NONE->value);
        self::assertSame('Simplex', PdfDuplex::SIMPLEX->value);
    }
}
