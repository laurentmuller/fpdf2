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

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PdfDocument::class)]
class PdfDocFileFontTest extends AbstractPdfDocTestCase
{
    private const FONTS_DIR = __DIR__ . '/resources/';

    public function testNoNameFont(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        $doc->addFont(
            family: 'Test',
            file: 'font_no_name.php',
            dir: self::FONTS_DIR
        );
        self::fail('A PDF exception must be raised.');
    }

    public function testOtherFont(): void
    {
        $doc = $this->createDocument();
        $doc->addFont(
            family: 'Test',
            file: 'font_test.php',
            dir: self::FONTS_DIR
        );
        self::assertSame(1, $doc->getPage());
    }
}
