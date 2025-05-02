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
    /**
     * @phpstan-return \Generator<int, array{string, PdfFontName|null}>
     */
    public static function getTryFromFamily(): \Generator
    {
        yield ['', null];
        yield ['fake', null];
        yield ['arial', PdfFontName::ARIAL];
        yield ['Arial', PdfFontName::ARIAL];
        yield ['ARIAL', PdfFontName::ARIAL];
    }

    /**
     * @phpstan-return \Generator<int, array{PdfFontName, bool}>
     */
    public static function getUseRegular(): \Generator
    {
        yield [PdfFontName::ARIAL, false];
        yield [PdfFontName::COURIER, false];
        yield [PdfFontName::HELVETICA, false];
        yield [PdfFontName::SYMBOL, true];
        yield [PdfFontName::TIMES, false];
        yield [PdfFontName::ZAPFDINGBATS, true];
    }

    /**
     * @phpstan-return \Generator<int, array{PdfFontName, string}>
     */
    public static function getValues(): \Generator
    {
        yield [PdfFontName::ARIAL, 'Arial'];
        yield [PdfFontName::COURIER, 'Courier'];
        yield [PdfFontName::HELVETICA, 'Helvetica'];
        yield [PdfFontName::SYMBOL, 'Symbol'];
        yield [PdfFontName::TIMES, 'Times'];
        yield [PdfFontName::ZAPFDINGBATS, 'ZapfDingbats'];
    }

    #[DataProvider('getTryFromFamily')]
    public function testTryFromFamily(string $family, ?PdfFontName $expected): void
    {
        $actual = PdfFontName::tryFromFamily($family);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('getUseRegular')]
    public function testUseRegular(PdfFontName $name, bool $expected): void
    {
        $actual = $name->useRegular();
        self::assertSame($expected, $actual);
    }

    #[DataProvider('getValues')]
    public function testValue(PdfFontName $name, string $expected): void
    {
        $actual = $name->value;
        self::assertSame($expected, $actual);
    }
}
