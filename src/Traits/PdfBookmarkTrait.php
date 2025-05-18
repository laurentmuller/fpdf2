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

namespace fpdf\Traits;

use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfPageMode;
use fpdf\Enums\PdfTextAlignment;
use fpdf\Enums\PdfUnit;
use fpdf\PdfDocument;
use fpdf\PdfException;

/**
 * Trait to handle bookmarks and page index.
 *
 * The code is inspired from FPDF script
 * <a href="http://www.fpdf.org/en/script/script1.php" target="_blank">Bookmarks</a>.
 *
 * @phpstan-type PdfBookmarkType = array{
 *      text: string,
 *      level: non-negative-int,
 *      y: float,
 *      page: int,
 *      link: int|null,
 *      hierarchy: array<string, int>}
 *
 * @phpstan-require-extends PdfDocument
 */
trait PdfBookmarkTrait
{
    /**
     * The space, in millimeters, between texts.
     */
    private const SPACE = 1.25;

    /**
     * The bookmark root object number.
     */
    private int $bookmarkRoot = -1;

    /**
     * The bookmarks.
     *
     * @phpstan-var array<int, PdfBookmarkType>
     */
    private array $bookmarks = [];

    /**
     * The page formatter.
     */
    private ?\NumberFormatter $pageFormatter = null;

    /**
     * Add a bookmark.
     *
     * @param string $text       the bookmark text
     * @param bool   $isUTF8     indicates if the text is encoded in ISO-8859-1 (false) or UTF-8 (true)
     * @param int    $level      the outline level (0 is the top level, 1 is just below, and so on)
     * @param bool   $currentY   indicate if the ordinate of the outline destination in the current page
     *                           is the current position (true) or the top of the page (false)
     * @param bool   $createLink true to create and add a link at the given ordinate position and page
     *
     * @throws PdfException if the given level is invalid. A level is not valid if:
     *                      <ul>
     *                      <li>Is smaller than 0.</li>
     *                      <li>Is greater than the level of the previous bookmark plus 1. For example, if the previous
     *                      bookmark is 2, the allowed values are 0..3.</li>
     *                      </ul>
     *
     * @phpstan-param non-negative-int $level
     */
    public function addBookmark(
        string $text,
        bool $isUTF8 = false,
        int $level = 0,
        bool $currentY = true,
        bool $createLink = true
    ): static {
        // validate
        $this->validateLevel($level);
        // convert
        if (!$isUTF8) {
            $text = $this->convertIsoToUtf8($text);
        }
        // add
        $page = $this->page;
        $y = $currentY ? $this->y : 0.0;
        $link = $createLink ? $this->createLink($y, $page) : null;
        $y = $this->scaleY($y);
        $this->bookmarks[] = [
            'text' => $text,
            'level' => $level,
            'y' => $y,
            'page' => $page,
            'link' => $link,
            'hierarchy' => [],
        ];

        return $this;
    }

    /**
     * Add an index page (as the new page) containing all bookmarks.
     *
     * The title and content use the given font name and font sizes. For title, the bold style is used.
     * Each line contains the text on the left, the page number on the right and is separated by
     * the given separator characters.
     *
     * After calling this function, the font name, the style, and the size are restored to the previous values.
     *
     * <b>Remark:</b> Do nothing if no bookmark is set.
     *
     * @param string                  $title       the index title. If empty (''), no title is output.
     * @param PdfFontName|string|null $fontName    the title and content font name. It can be a font name
     *                                             enumeration or a name defined by <code>addFont()</code>.
     *                                             If <code>null</code>, the current font name is kept.
     * @param float                   $titleSize   the title font size in points
     * @param float                   $contentSize the content font size in points
     * @param bool                    $addBookmark true to add the index page itself in the list of the bookmarks
     * @param string                  $separator   the separator character used between the bookmark text and
     *                                             the page number
     */
    public function addPageIndex(
        string $title = 'Index',
        PdfFontName|string|null $fontName = null,
        float $titleSize = 9.0,
        float $contentSize = 9.0,
        bool $addBookmark = true,
        string $separator = '.'
    ): static {
        // empty?
        if ([] === $this->bookmarks) {
            return $this;
        }

        // save font
        $oldFamily = $this->fontFamily;
        $oldStyle = $this->fontStyle;
        $oldSize = $this->fontSizeInPoint;

        // title
        $this->addPage();
        $titleBookmark = false;
        $space = PdfUnit::MILLIMETER->convert(self::SPACE, $this->unit);
        if ('' !== $title) {
            $this->setFont($fontName, PdfFontStyle::BOLD, $titleSize);
            $titleBookmark = $this->outputIndexTitle($title, $addBookmark, $space);
        }

        // content style
        $this->setFont($fontName, PdfFontStyle::REGULAR, $contentSize);
        $height = $this->getFontSize() + $space;
        $printable_width = $this->getPrintableWidth();
        if ('' === $separator) {
            $separator = ' ';
        }

        // bookmarks
        foreach ($this->bookmarks as $bookmark) {
            // skip title bookmark
            if ($titleBookmark === $bookmark) {
                continue;
            }

            // page text and size
            $page_text = $this->formatBookmarkPage($bookmark['page']);
            $page_size = $this->getStringWidth($page_text) + $space;
            // level offset
            $offset = $this->outputIndexLevel($bookmark['level'], $space);
            // text
            $link = $bookmark['link'];
            $width = $printable_width - $offset - $page_size - $space;
            $text_size = $this->outputIndexText($this->cleanText($bookmark['text']), $width, $height, $link, $space);
            // separator
            $width -= $text_size + $space;
            $this->outputIndexSeparator($separator, $width, $height, $link, $space);
            // page
            $this->outputIndexPage($page_text, $page_size, $height, $link);
        }

        // restore font
        $this->setFont($oldFamily, $oldStyle, $oldSize);

        return $this;
    }

    protected function putCatalog(): void
    {
        if ([] !== $this->bookmarks) {
            $this->setPageMode(PdfPageMode::USE_OUTLINES);
        }
        parent::putCatalog();
        if ([] !== $this->bookmarks) {
            $this->putf('/Outlines %d 0 R', $this->bookmarkRoot);
        }
    }

    protected function putResources(): void
    {
        parent::putResources();
        if ([] === $this->bookmarks) {
            return;
        }
        $number = $this->objectNumber + 1;
        $lastReference = $this->updateBookmarks();
        foreach ($this->bookmarks as $bookmark) {
            $this->putBookmark($bookmark, $number);
        }
        $this->putNewObj();
        $this->bookmarkRoot = $this->objectNumber;
        $this->putf('<</Type /Outlines /First %d 0 R', $number);
        $this->putf('/Last %d 0 R>>', $number + $lastReference);
        $this->putEndObj();
    }

    private function formatBookmarkPage(int $page): string
    {
        if (!$this->pageFormatter instanceof \NumberFormatter) {
            $this->pageFormatter = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL);
            $this->pageFormatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);
        }

        return (string) $this->pageFormatter->format($page);
    }

    private function outputIndexLevel(int $level, float $space): float
    {
        if (0 === $level) {
            return 0.0;
        }
        $offset = (float) $level * 2.0 * $space;
        $this->x += $offset;

        return $offset;
    }

    private function outputIndexPage(string $text, float $width, float $height, ?int $link): void
    {
        $this->cell(
            width: $width,
            height: $height,
            text: $text,
            move: PdfMove::NEW_LINE,
            align: PdfTextAlignment::RIGHT,
            link: $link
        );
    }

    private function outputIndexSeparator(
        string $separator,
        float $width,
        float $height,
        ?int $link,
        float $space
    ): void {
        $count = (int) \floor($width / $this->getStringWidth($separator));
        $width += $space;
        if ($count > 0) {
            $text = \str_repeat($separator, $count);
            $this->cell(
                width: $width,
                height: $height,
                text: $text,
                align: PdfTextAlignment::RIGHT,
                link: $link
            );
        } else {
            $this->x += $width;
        }
    }

    private function outputIndexText(
        string $text,
        float $width,
        float $height,
        ?int $link,
        float $space
    ): float {
        $text_width = $this->getStringWidth($text);
        while ($text_width > $width && '' !== $text) {
            $text = \substr($text, 0, -1);
            $text_width = $this->getStringWidth($text);
        }
        $this->cell(
            width: $text_width + $space,
            height: $height,
            text: $text,
            link: $link
        );

        return $text_width;
    }

    /**
     * @phpstan-param non-empty-string $title
     *
     * @phpstan-return PdfBookmarkType|false
     */
    private function outputIndexTitle(
        string $title,
        bool $addBookmark,
        float $space
    ): array|false {
        $result = false;
        if ($addBookmark) {
            $this->addBookmark($title, currentY: false);
            $result = \end($this->bookmarks);
        }
        $this->cell(
            height: $this->getFontSize() + $space,
            text: $title,
            move: PdfMove::NEW_LINE,
            align: PdfTextAlignment::CENTER
        );

        return $result;
    }

    /**
     * @phpstan-param PdfBookmarkType $bookmark
     */
    private function putBookmark(array $bookmark, int $number): void
    {
        $this->putNewObj();
        $this->putf('<</Title %s', $this->textString($bookmark['text']));
        foreach ($bookmark['hierarchy'] as $key => $value) {
            $this->putf('/%s %d 0 R', $key, $number + $value);
        }
        $pageNumber = $this->pageInfos[$bookmark['page']]['number'];
        $this->putf('/Dest [%d 0 R /XYZ 0 %.2F null]', $pageNumber, $bookmark['y']);
        $this->put('/Count 0>>');
        $this->putEndObj();
    }

    private function updateBookmarks(): int
    {
        $level = 0;
        $references = [];
        $count = \count($this->bookmarks);
        foreach ($this->bookmarks as $index => &$bookmark) {
            $currentLevel = $bookmark['level'];
            if ($currentLevel > 0) {
                $parent = $references[$currentLevel - 1];
                $bookmark['hierarchy']['Parent'] = $parent;
                $this->bookmarks[$parent]['hierarchy']['Last'] = $index;
                if ($currentLevel > $level) {
                    $this->bookmarks[$parent]['hierarchy']['First'] = $index;
                }
            } else {
                $bookmark['hierarchy']['Parent'] = $count;
            }
            if ($currentLevel <= $level && $index > 0) {
                $prev = $references[$currentLevel];
                $bookmark['hierarchy']['Prev'] = $prev;
                $this->bookmarks[$prev]['hierarchy']['Next'] = $index;
            }
            $references[$currentLevel] = $index;
            $level = $currentLevel;
        }

        return $references[0];
    }

    /**
     * @throws PdfException
     */
    private function validateLevel(int $level): void
    {
        $maxLevel = 0;
        if ([] !== $this->bookmarks) {
            $bookmark = \end($this->bookmarks);
            $maxLevel = $bookmark['level'] + 1;
        }
        if ($level < 0 || $level > $maxLevel) {
            $allowed = \implode('...', \array_unique([0, $maxLevel]));
            throw PdfException::format('Invalid bookmark level: %d. Allowed value: %s.', $level, $allowed);
        }
    }
}
