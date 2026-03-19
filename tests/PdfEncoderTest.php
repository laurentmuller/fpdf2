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

use fpdf\PdfEncoder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PdfEncoderTest extends TestCase
{
    private PdfEncoder $encoder;

    #[Override]
    protected function setUp(): void
    {
        $this->encoder = new PdfEncoder();
    }

    public static function getEscapes(): Generator
    {
        yield ['\\', '\\\\'];
        yield ['(', '\\('];
        yield [')', '\\)'];
        yield ["\r", '\\r'];
        yield ['A', 'A'];
    }

    public function testConvertEncoding(): void
    {
        $source = 'Ascii';
        $expected = \mb_convert_encoding($source, 'UTF-8', 'ISO-8859-1');
        $actual = $this->encoder->convertEncoding($source, 'UTF-8', 'ISO-8859-1');
        self::assertSame($expected, $actual);
    }

    public function testConvertIsoToUtf8(): void
    {
        $source = 'Ascii';
        $expected = \mb_convert_encoding($source, 'UTF-8', 'ISO-8859-1');
        $actual = $this->encoder->convertIsoToUtf8($source);
        self::assertSame($expected, $actual);
    }

    public function testConvertUtf8ToUtf16(): void
    {
        $source = 'Ascii';
        $expected = "\xFE\xFF" . \mb_convert_encoding($source, 'UTF-16BE', 'UTF-8');
        $actual = $this->encoder->convertUtf8ToUtf16($source);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('getEscapes')]
    public function testEscape(string $source, string $expected): void
    {
        $actual = $this->encoder->escape($source);
        self::assertSame($expected, $actual);
    }

    public function testFormatDate(): void
    {
        $timestamp = 0;
        $actual = $this->encoder->formatDate($timestamp);
        self::assertSame("D:19700101010000+01'00'", $actual);
    }

    public function testIsAscii(): void
    {
        self::assertTrue($this->encoder->isAscii(\chr(65)));
        self::assertFalse($this->encoder->isAscii(\chr(1000)));
    }

    public function testTextString(): void
    {
        $source = 'A';
        $actual = $this->encoder->textString($source);
        self::assertSame('(A)', $actual);

        $source = \chr(1000);
        $expected = "(\xFE\xFF" . \mb_convert_encoding($source, 'UTF-16BE', 'UTF-8') . ')';
        $actual = $this->encoder->textString($source);
        self::assertSame($expected, $actual);
    }
}
