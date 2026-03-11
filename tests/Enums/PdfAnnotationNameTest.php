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

namespace fpdf\Tests\Enums;

use fpdf\Enums\PdfAnnotationName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PdfAnnotationNameTest extends TestCase
{
    /**
     * @phpstan-return \Generator<int, array{PdfAnnotationName, string}>
     */
    public static function getValues(): \Generator
    {
        yield [PdfAnnotationName::COMMENT, 'Comment'];
        yield [PdfAnnotationName::HELP, 'Help'];
        yield [PdfAnnotationName::INSERT, 'Insert'];
        yield [PdfAnnotationName::KEY, 'Key'];
        yield [PdfAnnotationName::NEW_PARAGRAPH, 'NewParagraph'];
        yield [PdfAnnotationName::NOTE, 'Note'];
        yield [PdfAnnotationName::PARAGRAPH, 'Paragraph'];
    }

    public function testDefault(): void
    {
        self::assertSame(PdfAnnotationName::NOTE, PdfAnnotationName::getDefault());
    }

    #[DataProvider('getValues')]
    public function testValue(PdfAnnotationName $name, string $expected): void
    {
        $actual = $name->value;
        self::assertSame($expected, $actual);
    }
}
