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

use fpdf\Enums\PdfVersion;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PdfVersionTest extends TestCase
{
    /**
     * @psalm-return \Generator<int, array{0: PdfVersion, 1: PdfVersion, 2: bool}>
     */
    public static function getSmallerVersions(): \Generator
    {
        yield [PdfVersion::VERSION_1_3, PdfVersion::VERSION_1_3, false];
        yield [PdfVersion::VERSION_1_4, PdfVersion::VERSION_1_3, false];

        yield [PdfVersion::VERSION_1_3, PdfVersion::VERSION_1_4, true];
        yield [PdfVersion::VERSION_1_4, PdfVersion::VERSION_1_5, true];
        yield [PdfVersion::VERSION_1_5, PdfVersion::VERSION_1_6, true];
        yield [PdfVersion::VERSION_1_6, PdfVersion::VERSION_1_7, true];
    }

    /**
     * @psalm-return \Generator<int, array{0: PdfVersion, 1: string}>
     */
    public static function getValues(): \Generator
    {
        yield [PdfVersion::VERSION_1_3, '1.3'];
        yield [PdfVersion::VERSION_1_4, '1.4'];
        yield [PdfVersion::VERSION_1_5, '1.5'];
        yield [PdfVersion::VERSION_1_6, '1.6'];
        yield [PdfVersion::VERSION_1_7, '1.7'];
    }

    public function testDefault(): void
    {
        self::assertSame(PdfVersion::VERSION_1_3, PdfVersion::getDefault());
    }

    #[DataProvider('getSmallerVersions')]
    public function testIsSmaller(PdfVersion $source, PdfVersion $other, bool $expected): void
    {
        $actual = $source->isSmaller($other);
        self::assertSame($expected, $actual);
    }

    #[DataProvider('getValues')]
    public function testValue(PdfVersion $version, string $expected): void
    {
        $actual = $version->value;
        self::assertSame($expected, $actual);
    }
}
