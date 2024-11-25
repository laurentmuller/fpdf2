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

use fpdf\Enums\PdfFontName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PdfFontNameTest extends TestCase
{
    public static function getTryFromFamily(): \Generator
    {
        yield ['', null];
        yield ['fake', null];
        yield ['arial', PdfFontName::ARIAL];
        yield ['Arial', PdfFontName::ARIAL];
        yield ['ARIAL', PdfFontName::ARIAL];
    }

    #[DataProvider('getTryFromFamily')]
    public function testTryFromFamily(string $family, ?PdfFontName $expected): void
    {
        $actual = PdfFontName::tryFromFamily($family);
        self::assertSame($expected, $actual);
    }

    public function testValue(): void
    {
        self::assertSame('Arial', PdfFontName::ARIAL->value);
        self::assertSame('Courier', PdfFontName::COURIER->value);
        self::assertSame('Helvetica', PdfFontName::HELVETICA->value);
        self::assertSame('Symbol', PdfFontName::SYMBOL->value);
        self::assertSame('Times', PdfFontName::TIMES->value);
        self::assertSame('ZapfDingbats', PdfFontName::ZAPFDINGBATS->value);
    }
}
