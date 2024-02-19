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

namespace fpdf;

use PHPUnit\Framework\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(PdfFontStyle::class)]
class PdfFontStyleTest extends TestCase
{
    public static function getFrom(): \Iterator
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

    public static function getFromString(): \Iterator
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

    #[\PHPUnit\Framework\Attributes\DataProvider('getFrom')]
    public function testFrom(string $style, PdfFontStyle $expected, bool $exception = false): void
    {
        if ($exception) {
            self::expectException(\ValueError::class);
        }
        $actual = PdfFontStyle::from($style);
        self::assertSame($expected, $actual);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getFromString')]
    public function testFromString(?string $str, PdfFontStyle $expected): void
    {
        $actual = PdfFontStyle::fromString($str);
        self::assertSame($expected, $actual);
    }
}
