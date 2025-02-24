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

namespace fpdf\Tests;

use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfMove;
use fpdf\PdfDocument;
use fpdf\PdfException;

class PdfCompareFontTest extends AbstractCompareTestCase
{
    private const FONTS_DIR = __DIR__ . '/fonts/';

    public function testAddDirectoryFontName(): void
    {
        $doc = new PdfDocument();
        $dir = __DIR__ . '/../src/font';
        $doc->addFont(PdfFontName::COURIER, file: 'courier.php', dir: $dir);
        self::assertTrue($doc->isAutoPageBreak());
    }

    public function testAddFontName(): void
    {
        $doc = new PdfDocument();
        $doc->addFont(PdfFontName::COURIER);
        self::assertTrue($doc->isAutoPageBreak());
        $doc->addFont(PdfFontName::COURIER);
        self::assertTrue($doc->isAutoPageBreak());
    }

    public function testInvalidFontName(): void
    {
        self::expectException(PdfException::class);
        $doc = new PdfDocument();
        $doc->addFont('Fake', file: 'Invalid character/');
    }

    /**
     * @throws PdfException
     */
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $callback = function (string $base_name, string $file_name, bool $add_page) use ($doc): void {
            $style = match (true) {
                \str_ends_with($file_name, 'BoldItalic') => PdfFontStyle::BOLD_ITALIC,
                \str_ends_with($file_name, 'Italic') => PdfFontStyle::ITALIC,
                \str_ends_with($file_name, 'Bold') => PdfFontStyle::BOLD,
                default => PdfFontStyle::REGULAR
            };
            if ($add_page) {
                $doc->addPage();
            }
            $doc->addFont($file_name, $style, $base_name, self::FONTS_DIR);
            foreach (\range(10.0, 24.0, 2.0) as $size) {
                $doc->setFont($file_name, $style, $size);
                $doc->cell(
                    null,
                    $size,
                    \sprintf('Font name "%s" with size: "%.0f".', $file_name, $size),
                    move: PdfMove::BELOW
                );
            }
        };
        $this->applyFonts($callback);
    }

    #[\Override]
    protected function updateOldDocument(FPDF $doc): void
    {
        $callback = function (string $base_name, string $file_name, bool $add_page) use ($doc): void {
            $style = match (true) {
                \str_ends_with($file_name, 'BoldItalic') => 'BI',
                \str_ends_with($file_name, 'Italic') => 'I',
                \str_ends_with($file_name, 'Bold') => 'B',
                default => ''
            };
            if ($add_page) {
                $doc->AddPage();
            }
            $doc->AddFont($file_name, $style, $base_name, self::FONTS_DIR);
            foreach (\range(10.0, 24.0, 2.0) as $size) {
                $doc->SetFont($file_name, $style, $size);
                $doc->Cell(
                    0.0,
                    $size,
                    \sprintf('Font name "%s" with size: "%.0f".', $file_name, $size),
                    ln: 1
                );
            }
        };
        $this->applyFonts($callback);
    }

    /**
     * @phpstan-param callable(string, string, bool):void $callable
     */
    private function applyFonts(callable $callable): void
    {
        $pattern = self::FONTS_DIR . '*.php';
        $files = \glob($pattern);
        self::assertIsArray($files);
        self::assertNotEmpty($files);

        $add_page = false;
        foreach ($files as $file) {
            $base_name = \pathinfo($file, \PATHINFO_BASENAME);
            $file_name = \pathinfo($file, \PATHINFO_FILENAME);
            $callable($base_name, $file_name, $add_page);
            $add_page = true;
        }
    }
}
