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

use fpdf\Enums\PdfFontStyle;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PdfFontStyleTest extends TestCase
{
    /**
     * @psalm-return \Generator<int, array{0: string, 1: PdfFontStyle, 2?: true}>
     */
    public static function getFrom(): \Generator
    {
        yield ['b', PdfFontStyle::BOLD];
        yield ['bi', PdfFontStyle::BOLD_ITALIC];
        yield ['biu', PdfFontStyle::BOLD_ITALIC_UNDERLINE];
        yield ['bu', PdfFontStyle::BOLD_UNDERLINE];
        yield ['i', PdfFontStyle::ITALIC];
        yield ['iu', PdfFontStyle::ITALIC_UNDERLINE];
        yield ['u', PdfFontStyle::UNDERLINE];
        yield ['', PdfFontStyle::REGULAR];
        yield ['B', PdfFontStyle::BOLD, true];
        yield ['Z', PdfFontStyle::REGULAR, true];
    }

    /**
     * @psalm-return \Generator<int, array{0: string|null, 1: PdfFontStyle}>
     */
    public static function getFromString(): \Generator
    {
        yield ['b', PdfFontStyle::BOLD];
        yield ['B', PdfFontStyle::BOLD];
        yield ['bi', PdfFontStyle::BOLD_ITALIC];
        yield ['ib', PdfFontStyle::BOLD_ITALIC];
        yield ['biu', PdfFontStyle::BOLD_ITALIC_UNDERLINE];
        yield ['iub', PdfFontStyle::BOLD_ITALIC_UNDERLINE];
        yield ['ubi', PdfFontStyle::BOLD_ITALIC_UNDERLINE];
        yield ['uib', PdfFontStyle::BOLD_ITALIC_UNDERLINE];
        yield ['bu', PdfFontStyle::BOLD_UNDERLINE];
        yield ['uB', PdfFontStyle::BOLD_UNDERLINE];
        yield ['i', PdfFontStyle::ITALIC];
        yield ['I', PdfFontStyle::ITALIC];
        yield ['iu', PdfFontStyle::ITALIC_UNDERLINE];
        yield ['ui', PdfFontStyle::ITALIC_UNDERLINE];
        yield ['u', PdfFontStyle::UNDERLINE];
        yield ['U', PdfFontStyle::UNDERLINE];
        yield [null, PdfFontStyle::REGULAR];
        yield ['', PdfFontStyle::REGULAR];
        yield ['z', PdfFontStyle::REGULAR];
        yield ['BBB', PdfFontStyle::BOLD];
        yield ['BIBI', PdfFontStyle::BOLD_ITALIC];
        yield ['bibi', PdfFontStyle::BOLD_ITALIC];
    }

    /**
     * @psalm-return \Generator<int, array{0: PdfFontStyle, 1: bool}>
     */
    public static function getIsUnderline(): \Generator
    {
        yield [PdfFontStyle::BOLD, false];
        yield [PdfFontStyle::BOLD_ITALIC, false];
        yield [PdfFontStyle::BOLD_ITALIC_UNDERLINE, true];
        yield [PdfFontStyle::BOLD_UNDERLINE, true];
        yield [PdfFontStyle::ITALIC, false];
        yield [PdfFontStyle::ITALIC_UNDERLINE, true];
        yield [PdfFontStyle::REGULAR, false];
        yield [PdfFontStyle::UNDERLINE, true];
    }

    /**
     * @psalm-return \Generator<int, array{0: PdfFontStyle, 1: PdfFontStyle}>
     */
    public static function getRemoveUnderline(): \Generator
    {
        yield [PdfFontStyle::BOLD, PdfFontStyle::BOLD];
        yield [PdfFontStyle::BOLD_ITALIC, PdfFontStyle::BOLD_ITALIC];
        yield [PdfFontStyle::BOLD_ITALIC_UNDERLINE, PdfFontStyle::BOLD_ITALIC];
        yield [PdfFontStyle::BOLD_UNDERLINE, PdfFontStyle::BOLD];
        yield [PdfFontStyle::ITALIC, PdfFontStyle::ITALIC];
        yield [PdfFontStyle::ITALIC_UNDERLINE, PdfFontStyle::ITALIC];
        yield [PdfFontStyle::REGULAR, PdfFontStyle::REGULAR];
        yield [PdfFontStyle::UNDERLINE, PdfFontStyle::REGULAR];
    }

    #[DataProvider('getFrom')]
    public function testFrom(string $style, PdfFontStyle $expected, bool $exception = false): void
    {
        if ($exception) {
            self::expectException(\ValueError::class);
        }
        $actual = PdfFontStyle::from($style);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('getFromString')]
    public function testFromString(?string $str, PdfFontStyle $expected): void
    {
        $actual = PdfFontStyle::fromString($str);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('getIsUnderline')]
    public function testIsUnderline(PdfFontStyle $style, bool $expected): void
    {
        $actual = $style->isUnderLine();
        self::assertSame($expected, $actual);
    }

    #[DataProvider('getRemoveUnderline')]
    public function testRemoveUnderline(PdfFontStyle $style, PdfFontStyle $expected): void
    {
        $actual = $style->removeUnderLine();
        self::assertSame($expected, $actual);
    }
}
