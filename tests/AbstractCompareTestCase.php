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

use fpdf\Enums\PdfDestination;
use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\PdfDocument;
use PHPUnit\Framework\TestCase;

abstract class AbstractCompareTestCase extends TestCase
{
    /**
     * Pattern to remove the creation date and the producer.
     */
    private const PATTERN = [
        '/\/CreationDate.*\)/mi',
        '/\/Producer \(FPDF.*\)/mi',
    ];

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        if (!\defined('FPDF_FONTPATH')) {
            \define('FPDF_FONTPATH', __DIR__ . '/../src/font/');
        }
    }

    public function testEqual(): void
    {
        $oldDocument = $this->createOldDocument();
        $this->updateOldDocument($oldDocument);
        /** @phpstan-var string $oldContent */
        $oldContent = $oldDocument->Output('S');
        $oldContent = \preg_replace(self::PATTERN, '', $oldContent);
        self::assertIsString($oldContent);

        $newDocument = $this->createNewDocument();
        $this->updateNewDocument($newDocument);
        $newContent = $newDocument->output(PdfDestination::STRING);
        $newContent = \preg_replace(self::PATTERN, '', $newContent);
        self::assertIsString($newContent);

        self::assertSame($oldContent, $newContent);
    }

    protected function createNewDocument(): PdfDocument
    {
        $doc = new PdfDocument();
        $doc->setCompression(false);
        $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR, 9.0);
        $doc->addPage();

        return $doc;
    }

    protected function createOldDocument(): FPDF
    {
        $doc = new FPDF();
        $doc->SetCompression(false);
        $doc->SetFont('Arial', '', 9.0);
        $doc->AddPage();

        return $doc;
    }

    abstract protected function updateNewDocument(PdfDocument $doc): void;

    abstract protected function updateOldDocument(FPDF $doc): void;
}
