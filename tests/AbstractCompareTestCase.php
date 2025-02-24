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
use fpdf\PdfException;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

abstract class AbstractCompareTestCase extends TestCase
{
    protected bool $unlink = true;

    private string $newFile = '';
    private string $oldFile = '';

    #[\Override]
    public static function setUpBeforeClass(): void
    {
        if (!\defined('FPDF_FONTPATH')) {
            \define('FPDF_FONTPATH', __DIR__ . '/../src/font/');
        }
    }

    #[\Override]
    protected function setUp(): void
    {
        $this->newFile = __DIR__ . '/doc_new.pdf';
        $this->oldFile = __DIR__ . '/doc_old.pdf';
    }

    #[Depends('testOld')]
    #[Depends('testNew')]
    public function testEqual(): void
    {
        self::assertFileExists($this->oldFile);
        self::assertFileExists($this->newFile);

        $old_content = \file_get_contents($this->oldFile);
        $new_content = \file_get_contents($this->newFile);

        if ($this->unlink) {
            \unlink($this->oldFile);
            \unlink($this->newFile);
        }

        self::assertIsString($old_content);
        self::assertIsString($new_content);

        // remove creation date and version
        $patterns = [
            '/\/CreationDate.*\)/mi',
            '/\/Producer \(FPDF.*\)/mi',
        ];
        $old_content = \preg_replace($patterns, '', $old_content);
        $new_content = \preg_replace($patterns, '', $new_content);

        self::assertSame($old_content, $new_content);
    }

    /**
     * @throws PdfException
     */
    public function testNew(): void
    {
        $doc = $this->createNewDocument();
        $this->updateNewDocument($doc);
        $doc->output(PdfDestination::FILE, $this->newFile);
        self::assertFileExists($this->newFile);
    }

    public function testOld(): void
    {
        $doc = $this->createOldDocument();
        $this->updateOldDocument($doc);
        $doc->Output('F', $this->oldFile);
        self::assertFileExists($this->oldFile);
    }

    /**
     * @throws PdfException
     */
    protected function createNewDocument(): PdfDocument
    {
        $doc = new PdfDocument();
        $doc->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR, 9.0);
        $doc->addPage();

        return $doc;
    }

    protected function createOldDocument(): FPDF
    {
        $doc = new FPDF();
        $doc->SetFont('Arial', '', 9.0);
        $doc->AddPage();

        return $doc;
    }

    /**
     * @throws PdfException
     */
    abstract protected function updateNewDocument(PdfDocument $doc): void;

    abstract protected function updateOldDocument(FPDF $doc): void;
}
