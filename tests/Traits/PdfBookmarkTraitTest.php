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

namespace fpdf\Tests\Traits;

use fpdf\Enums\PdfDestination;
use fpdf\Enums\PdfFontName;
use fpdf\PdfException;
use fpdf\Tests\fixture\PdfDocumentBookmark;
use PHPUnit\Framework\TestCase;

class PdfBookmarkTraitTest extends TestCase
{
    public function testBookmarkEmpty(): void
    {
        $doc = $this->createDocument();
        $doc->addPageIndex();
        $doc->output(PdfDestination::STRING);
        self::assertSame(1, $doc->getPage());
    }

    public function testBookmarks(): void
    {
        $doc = $this->createDocument();
        $doc->addBookmark('Level 0');
        $doc->addBookmark('Level 1', level: 1);
        $doc->addPageIndex();
        $doc->output(PdfDestination::STRING);
        self::assertSame(2, $doc->getPage());
    }

    public function testBookmarksWithSeparator(): void
    {
        $doc = $this->createDocument();
        $doc->addBookmark('Level 0');
        $doc->addBookmark('Level 1', level: 1);
        $doc->addPageIndex(separator: '');
        $doc->output(PdfDestination::STRING);
        self::assertSame(2, $doc->getPage());
    }

    public function testIndexNoTitle(): void
    {
        $doc = $this->createDocument();
        $doc->addBookmark('Level 0');
        $doc->addBookmark('Level 1', level: 1);
        $doc->addPageIndex(title: '');
        $doc->output(PdfDestination::STRING);
        self::assertSame(2, $doc->getPage());
    }

    public function testLevelInvalid(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid bookmark level: 3. Allowed value: 0.');
        $doc = $this->createDocument();
        $doc->addBookmark('Invalid Level', level: 3);
    }

    /**
     * @psalm-suppress InvalidArgument
     */
    public function testLevelNegative(): void
    {
        self::expectException(PdfException::class);
        $doc = $this->createDocument();
        // @phpstan-ignore argument.type
        $doc->addBookmark('Negative Level', level: -1);
    }

    public function testLongBookmark(): void
    {
        $bookmark = \str_repeat('Bookmark', 30);
        $doc = $this->createDocument(100);
        $doc->addBookmark($bookmark);
        $doc->addPageIndex();
        $doc->output(PdfDestination::STRING);
        self::assertSame(2, $doc->getPage());
    }

    public function testLongSeparator(): void
    {
        $separator = \str_repeat('Separator', 30);
        $doc = $this->createDocument(100);
        $doc->addBookmark('Level 0');
        $doc->addPageIndex(separator: $separator);
        $doc->output(PdfDestination::STRING);
        self::assertSame(2, $doc->getPage());
    }

    private function createDocument(?float $rightMargin = null): PdfDocumentBookmark
    {
        $doc = new PdfDocumentBookmark();
        $doc->setFont(PdfFontName::ARIAL);
        if (null !== $rightMargin) {
            $doc->setRightMargin(100);
        }
        $doc->addPage();

        return $doc;
    }
}
