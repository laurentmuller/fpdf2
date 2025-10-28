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

use fpdf\Enums\PdfLayout;
use fpdf\Enums\PdfVersion;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PdfLayoutTest extends TestCase
{
    /**
     * @phpstan-return \Generator<int, array{PdfLayout, PdfVersion}>
     */
    public static function getVersions(): \Generator
    {
        yield [PdfLayout::DEFAULT, PdfVersion::VERSION_1_3];
        yield [PdfLayout::ONE_COLUMN, PdfVersion::VERSION_1_3];
        yield [PdfLayout::SINGLE_PAGE, PdfVersion::VERSION_1_3];
        yield [PdfLayout::TWO_COLUMN_LEFT, PdfVersion::VERSION_1_3];
        yield [PdfLayout::TWO_COLUMN_RIGHT, PdfVersion::VERSION_1_3];
        yield [PdfLayout::TWO_PAGE_LEFT, PdfVersion::VERSION_1_5];
        yield [PdfLayout::TWO_PAGE_RIGHT, PdfVersion::VERSION_1_5];
    }

    public function testDefault(): void
    {
        self::assertSame(PdfLayout::DEFAULT, PdfLayout::getDefault());
    }

    public function testValue(): void
    {
        self::assertSame('', PdfLayout::DEFAULT->value);
        self::assertSame('SinglePage', PdfLayout::SINGLE_PAGE->value);
        self::assertSame('OneColumn', PdfLayout::ONE_COLUMN->value);
        self::assertSame('TwoColumnLeft', PdfLayout::TWO_COLUMN_LEFT->value);
        self::assertSame('TwoColumnRight', PdfLayout::TWO_COLUMN_RIGHT->value);
        self::assertSame('TwoPageLeft', PdfLayout::TWO_PAGE_LEFT->value);
        self::assertSame('TwoPageRight', PdfLayout::TWO_PAGE_RIGHT->value);
    }

    #[DataProvider('getVersions')]
    public function testVersion(PdfLayout $layout, PdfVersion $expected): void
    {
        $actual = $layout->getVersion();
        self::assertSame($expected, $actual);
    }
}
