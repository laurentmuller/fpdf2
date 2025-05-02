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

use fpdf\Enums\PdfPageMode;
use fpdf\Enums\PdfVersion;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PdfPageModeTest extends TestCase
{
    /**
     * @phpstan-return \Generator<int, array{PdfPageMode, PdfVersion}>
     */
    public static function getVersions(): \Generator
    {
        yield [PdfPageMode::FULL_SCREEN, PdfVersion::VERSION_1_3];
        yield [PdfPageMode::USE_ATTACHMENTS, PdfVersion::VERSION_1_6];
        yield [PdfPageMode::USE_NONE, PdfVersion::VERSION_1_3];
        yield [PdfPageMode::USE_OC, PdfVersion::VERSION_1_5];
        yield [PdfPageMode::USE_OUTLINES, PdfVersion::VERSION_1_3];
        yield [PdfPageMode::USE_THUMBS, PdfVersion::VERSION_1_3];
    }

    public function testDefault(): void
    {
        self::assertSame(PdfPageMode::USE_NONE, PdfPageMode::getDefault());
    }

    public function testValue(): void
    {
        self::assertSame('FullScreen', PdfPageMode::FULL_SCREEN->value);
        self::assertSame('UseAttachments', PdfPageMode::USE_ATTACHMENTS->value);
        self::assertSame('UseNone', PdfPageMode::USE_NONE->value);
        self::assertSame('UseOC', PdfPageMode::USE_OC->value);
        self::assertSame('UseOutlines', PdfPageMode::USE_OUTLINES->value);
        self::assertSame('UseThumbs', PdfPageMode::USE_THUMBS->value);
    }

    #[DataProvider('getVersions')]
    public function testVersion(PdfPageMode $pageMode, PdfVersion $expected): void
    {
        $actual = $pageMode->getVersion();
        self::assertSame($expected, $actual);
    }
}
