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

use fpdf\Enums\PdfBlendMode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PdfBlendModeTest extends TestCase
{
    /**
     * @phpstan-return \Generator<int, array{PdfBlendMode, string}>
     */
    public static function getValues(): \Generator
    {
        yield [PdfBlendMode::COLOR, 'Color'];
        yield [PdfBlendMode::COLOR_BURN, 'ColorBurn'];
        yield [PdfBlendMode::COLOR_DODGE, 'ColorDodge'];
        yield [PdfBlendMode::DARKEN, 'Darken'];
        yield [PdfBlendMode::DIFFERENCE, 'Difference'];
        yield [PdfBlendMode::EXCLUSION, 'Exclusion'];
        yield [PdfBlendMode::HARD_LIGHT, 'HardLight'];
        yield [PdfBlendMode::HUE, 'Hue'];
        yield [PdfBlendMode::LIGHTEN, 'Lighten'];
        yield [PdfBlendMode::LUMINOSITY, 'Luminosity'];
        yield [PdfBlendMode::MULTIPLY, 'Multiply'];
        yield [PdfBlendMode::NORMAL, 'Normal'];
        yield [PdfBlendMode::OVERLAY, 'Overlay'];
        yield [PdfBlendMode::SATURATION, 'Saturation'];
        yield [PdfBlendMode::SCREEN, 'Screen'];
        yield [PdfBlendMode::SOFT_LIGHT, 'SoftLight'];
    }

    #[DataProvider('getValues')]
    public function testValue(PdfBlendMode $mode, string $expected): void
    {
        $actual = $mode->value;
        self::assertSame($expected, $actual);
    }
}
