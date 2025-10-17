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
use fpdf\Tests\Fixture\PdfDocumentBookmark;
use PHPUnit\Framework\TestCase;

class PdfBookmarkTraitTest extends TestCase
{
    public function testLevelInvalidRange(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid bookmark level: 3. Allowed value(s): 0 to 2.');
        $doc = $this->createDocument();
        $doc->addBookmark(text: 'Level 0');
        $doc->addBookmark(text: 'Level 1', level: 1);
        $doc->addBookmark(text: 'Invalid Level', level: 3);
    }

    public function testLevelInvalidSingle(): void
    {
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid bookmark level: 3. Allowed value(s): 0.');
        $doc = $this->createDocument();
        $doc->addBookmark(text: 'Invalid Level', level: 3);
    }

    /**
     * @psalm-suppress InvalidArgument
     */
    public function testLevelNegative(): void
    {
        $level = -1;
        self::expectException(PdfException::class);
        self::expectExceptionMessage('Invalid bookmark level: -1. Allowed value(s): 0.');
        $doc = $this->createDocument();
        // @phpstan-ignore argument.type
        $doc->addBookmark(text: 'Negative Level', level: $level);
    }

    public function testLongBookmark(): void
    {
        $text = \str_repeat('Bookmark', 30);
        $doc = $this->createDocument(100);
        $doc->addBookmark(text: $text);
        $doc->addPageIndex();
        $doc->output(PdfDestination::STRING);
        self::assertSame(2, $doc->getPage());
    }

    public function testLongSeparator(): void
    {
        $separator = \str_repeat('Separator', 30);
        $doc = $this->createDocument(100);
        $doc->addBookmark(text: 'Level 0');
        $doc->addPageIndex(separator: $separator);
        $doc->output(PdfDestination::STRING);
        self::assertSame(2, $doc->getPage());
    }

    public function testPageIndexWithBookmarks(): void
    {
        $doc = $this->createDocument();
        $doc->addBookmark(text: 'Level 0');
        $doc->addBookmark(text: 'Level 1', level: 1);
        $doc->addPageIndex();
        $doc->output(PdfDestination::STRING);
        self::assertSame(2, $doc->getPage());
    }

    public function testPageIndexWithoutBookmark(): void
    {
        $doc = $this->createDocument();
        $doc->addPageIndex();
        $doc->output(PdfDestination::STRING);
        self::assertSame(1, $doc->getPage());
    }

    public function testPageIndexWithoutSeparator(): void
    {
        $doc = $this->createDocument();
        $doc->addBookmark(text: 'Level 0');
        $doc->addBookmark(text: 'Level 1', level: 1);
        $doc->addPageIndex(separator: '');
        $doc->output(PdfDestination::STRING);
        self::assertSame(2, $doc->getPage());
    }

    public function testPageIndexWithoutTitle(): void
    {
        $doc = $this->createDocument();
        $doc->addBookmark('Level 0');
        $doc->addBookmark('Level 1', level: 1);
        $doc->addPageIndex(title: '');
        $doc->output(PdfDestination::STRING);
        self::assertSame(2, $doc->getPage());
    }

    private function createDocument(?float $rightMargin = null): PdfDocumentBookmark
    {
        $doc = new PdfDocumentBookmark();
        $doc->setFont(PdfFontName::ARIAL);
        if (null !== $rightMargin) {
            $doc->setRightMargin($rightMargin);
        }
        $doc->addPage();

        return $doc;
    }
}
