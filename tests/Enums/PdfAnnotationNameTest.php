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

namespace Enums;

use fpdf\Enums\PdfAnnotationName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PdfAnnotationNameTest extends TestCase
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
