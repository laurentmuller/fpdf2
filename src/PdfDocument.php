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

/**
 * @phpstan-type PdfPageSizeType = array{
 *     0: float,
 *     1: float}
 * @phpstan-type PdfFontType = array{
 *     i: int,
 *     path: string,
 *     name: string,
 *     enc?: string,
 *     type: string,
 *     subsetted: bool,
 *     n: int,
 *     up: float,
 *     ut: float,
 *     uv?: array<int, int|int[]>,
 *     desc: array<string, string>,
 *     cw: array<string, float>,
 *     file: string,
 *     diff?: string,
 *     originalsize: string,
 *     size1: string,
 *     size2: string,
 *     length1: int,
 *     length2?: int}
 * @phpstan-type PdfPageInfoType = array{
 *     n: int,
 *     rotation: int,
 *     size?: PdfPageSizeType}
 * @phpstan-type PdfImageType = array{
 *     i: int,
 *     n: int,
 *     w: int,
 *     h: int,
 *     cs: string,
 *     bpc: int,
 *     f?: string,
 *     data: string,
 *     dp?: string,
 *     smask?: string,
 *     pal: string,
 *     trns?: string|array<int, string>}
 * @phpstan-type PageLinkType = array{
 *     0: float,
 *     1: float,
 *     2: float,
 *     3: float,
 *     4: int|string,
 *     5: int}
 */
class PdfDocument
{
    /**
     * The FPDF version.
     */
    public const VERSION = '1.86';

    /**
     * The array of core font names.
     */
    private const CORE_FONTS = [
        'courier',
        'helvetica',
        'times',
        'symbol',
        'zapfdingbats',
    ];

    private const STATE_CLOSED = 3;
    private const STATE_END_PAGE = 1;
    private const STATE_NO_PAGE = 0;
    private const STATE_PAGE_STARTED = 2;

    /**
     * The alias for total number of pages.
     */
    protected string $aliasNumberPages = '{nb}';
    /**
     * The automatic page breaking.
     */
    protected bool $autoPageBreak;
    /**
     * The bottom margin (page break margin).
     */
    protected float $bottomMargin;
    /**
     * The buffer holding in-memory PDF.
     */
    protected string $buffer = '';
    /**
     * The cell margin.
     */
    protected float $cellMargin;
    /**
     * The array of ToUnicode CMaps.
     *
     * @phpstan-var array<string, int>
     */
    protected array $cMaps = [];
    /**
     * Indicates whether fill and text colors are different.
     */
    protected bool $colorFlag = false;
    /**
     * The compression flag.
     */
    protected bool $compression;
    /**
     * The document creation date.
     */
    protected int $creationDate = 0;
    /**
     * The current font info.
     *
     * @phpstan-var PdfFontType|null array
     */
    protected ?array $currentFont = null;
    /**
     * The current orientation.
     */
    protected PdfOrientation $currentOrientation;
    /**
     * The current page size.
     *
     * @phpstan-var PdfPageSizeType
     */
    protected array $currentPageSize;
    /**
     * The current page rotation.
     */
    protected int $currentRotation = 0;
    /**
     * The default orientation.
     */
    protected PdfOrientation $defaultOrientation;
    /**
     * The default page size.
     *
     * @phpstan-var PdfPageSizeType
     */
    protected array $defaultPageSize;
    /**
     * The commands for drawing color.
     */
    protected string $drawColor = '0 G';
    /**
     * The array of encodings.
     *
     * @phpstan-var array<string, int>
     */
    protected array $encodings = [];
    /**
     * The commands for filling color.
     */
    protected string $fillColor = '0 G';
    /**
     * The current font family.
     */
    protected string $fontFamily = '';
    /**
     * The array of font files.
     *
     * @phpstan-var array<string, array>
     */
    protected array $fontFiles = [];
    /**
     * The directory containing fonts.
     */
    protected string $fontPath;
    /**
     * The array of used fonts.
     *
     * @phpstan-var array<string, PdfFontType>
     */
    protected array $fonts = [];
    /**
     * The current font size in user unit.
     */
    protected float $fontSize;
    /**
     * The current font size in points.
     */
    protected float $fontSizeInPoint = 12.0;
    /**
     * The current font style.
     */
    protected PdfFontStyle $fontStyle = PdfFontStyle::REGULAR;
    /**
     * The current page height in user unit.
     */
    protected float $height = 0.0;
    /**
     * The current page height in points.
     */
    protected float $heightInPoint;
    /**
     * The array of used images.
     *
     * @phpstan-var array<string, PdfImageType>
     */
    protected array $images = [];
    /**
     * The flag set when processing footer.
     */
    protected bool $inFooter = false;
    /**
     * The flag set when processing header.
     */
    protected bool $inHeader = false;
    /**
     * The height of last printed cell.
     */
    protected float $lastHeight = 0.0;
    /**
     * The layout display mode.
     */
    protected PdfLayout $layout;
    /**
     * The left margin.
     */
    protected float $leftMargin;
    /**
     * The line width in user unit.
     */
    protected float $lineWidth;
    /**
     * The array of internal links.
     *
     * @phpstan-var array<int, array<int|float>>
     */
    protected array $links = [];
    /**
     * The document properties.
     *
     * @phpstan-var array<string, string>
     */
    protected array $metadata;
    /**
     * The current object number.
     */
    protected int $objectNumber = 2;
    /**
     * The array of object offsets.
     *
     * @phpstan-var array<int, int>
     */
    protected array $offsets = [];
    /**
     * The current page number.
     */
    protected int $page = 0;
    /**
     * The threshold used to trigger page breaks.
     */
    protected float $pageBreakTrigger;
    /**
     * The page-related data.
     *
     * @phpstan-var array<int, PdfPageInfoType>
     */
    protected array $pageInfos = [];
    /**
     * The array of links in pages.
     *
     * @phpstan-var array<int, PageLinkType[]>
     */
    protected array $pageLinks = [];
    /**
     * The array containing pages.
     *
     * @phpstan-var array<int, string>
     */
    protected array $pages = [];
    /**
     * The PDF version number.
     */
    protected string $pdfVersion;
    /**
     * The right margin.
     */
    protected float $rightMargin;
    /**
     * The scale factor (number of points in user unit).
     */
    protected float $scaleFactor = 1.0;
    /**
     * The current document state.
     */
    protected int $state = self::STATE_NO_PAGE;
    /**
     * The commands for text color.
     */
    protected string $textColor = '0 G';
    /**
     * The top margin.
     */
    protected float $topMargin;
    /**
     * The underlining flag.
     */
    protected bool $underline = false;
    /**
     * The current page width in user unit.
     */
    protected float $width = 0;
    /**
     * The current page width in point.
     */
    protected float $widthInPoint;
    /**
     * Indicates whether alpha channel is used.
     */
    protected bool $withAlpha = false;
    /**
     * The word spacing.
     */
    protected float $wordSpacing = 0.0;
    /**
     * The current x position in user unit.
     */
    protected float $x = 0.0;
    /**
     * The current y position in user unit.
     */
    protected float $y = 0.0;
    /**
     * The zoom display mode.
     */
    protected PdfZoom|int $zoom;

    /**
     * @phpstan-param PdfSize|PdfPageSizeType $size
     */
    public function __construct(
        PdfOrientation $orientation = PdfOrientation::PORTRAIT,
        PdfUnit $unit = PdfUnit::MILLIMETER,
        PdfSize|array $size = PdfSize::A4
    ) {
        // Font path
        if (!\defined('FPDF_FONT_PATH')) {
            \define('FPDF_FONT_PATH', __DIR__ . '/font/');
        }
        $this->fontPath = (string) FPDF_FONT_PATH;

        // Scale factor
        $this->scaleFactor = $unit->getScaleFactor();
        // Page size
        $size = $this->getPageSize($size);
        $this->defaultPageSize = $size;
        $this->currentPageSize = $size;
        // Page orientation
        $this->defaultOrientation = $orientation;
        if (PdfOrientation::PORTRAIT === $orientation) {
            $this->width = $size[0];
            $this->height = $size[1];
        } else {
            $this->width = $size[1];
            $this->height = $size[0];
        }
        $this->currentOrientation = $this->defaultOrientation;
        $this->widthInPoint = $this->width * $this->scaleFactor;
        $this->heightInPoint = $this->height * $this->scaleFactor;
        // Page margins (1 cm)
        $margin = 28.35 / $this->scaleFactor;
        $this->setMargins($margin, $margin);
        // Interior cell margin (1 mm)
        $this->cellMargin = $margin / 10.0;
        // Line width (0.2 mm)
        $this->lineWidth = .567 / $this->scaleFactor;
        // Automatic page break
        $this->setAutoPageBreak(true, 2.0 * $margin);
        // Default display mode
        $this->setDisplayMode();
        // Enable compression
        $this->setCompression(true);
        // Metadata
        $this->metadata = ['Producer' => 'FPDF ' . self::VERSION];
        // Set default PDF version number
        $this->pdfVersion = '1.3';
        // font size and style
        $this->fontSize = $this->fontSizeInPoint / $this->scaleFactor;
        // creation date
        $this->creationDate = \time();
    }

    /**
     * @throws PdfException
     */
    public function addFont(
        string $family,
        PdfFontStyle $style = PdfFontStyle::REGULAR,
        string $file = '',
        string $dir = ''
    ): self {
        $family = \strtolower($family);
        if ('' === $file) {
            $file = \str_replace(' ', '', $family) . $style->value . '.php';
        }
        $fontKey = $family . $style->value;
        if (isset($this->fonts[$fontKey])) {
            return $this;
        }
        if (\str_contains($file, '/') || \str_contains($file, '\\')) {
            throw new PdfException(\sprintf('Incorrect font definition file name: %s.', $file));
        }
        if ('' === $dir) {
            $dir = $this->fontPath;
        }
        if (!\str_ends_with($dir, '/') && !\str_ends_with($dir, '\\')) {
            $dir .= '/';
        }
        /** @phpstan-var PdfFontType $info */
        $info = $this->loadFont($dir . $file);
        $info['i'] = \count($this->fonts) + 1;
        if (!empty($info['file'])) {
            // Embedded font
            $info['file'] = $dir . $info['file'];
            if ('TrueType' === $info['type']) {
                $this->fontFiles[$info['file']] = ['length1' => $info['originalsize']];
            } else {
                $this->fontFiles[$info['file']] = ['length1' => $info['size1'], 'length2' => $info['size2']];
            }
        }
        $this->fonts[$fontKey] = $info;

        return $this;
    }

    public function addLink(): int
    {
        $index = \count($this->links) + 1;
        $this->links[$index] = [0, 0];

        return $index;
    }

    /**
     * @phpstan-param PdfSize|PdfPageSizeType|null $size
     *
     * @throws PdfException
     */
    public function addPage(
        ?PdfOrientation $orientation = null,
        PdfSize|array|null $size = null,
        int $rotation = 0
    ): self {
        if (self::STATE_CLOSED === $this->state) {
            throw new PdfException('The document is closed.');
        }
        // Start a new page
        $fontFamily = $this->fontFamily;
        $fontStyle = $this->fontStyle;
        $fontSizeInPoint = $this->fontSizeInPoint;
        $lineWidth = $this->lineWidth;
        $drawColor = $this->drawColor;
        $fillColor = $this->fillColor;
        $textColor = $this->textColor;
        $colorFlag = $this->colorFlag;
        if ($this->page > 0) {
            // Page footer
            $this->inFooter = true;
            $this->footer();
            $this->inFooter = false;
            // Close page
            $this->endPage();
        }
        // Start new page
        $this->beginPage($orientation, $size, $rotation);
        // Set line cap style to square
        $this->out('2 J');
        // Set line width
        $this->lineWidth = $lineWidth;
        $this->outf('%.2F w', $lineWidth * $this->scaleFactor);
        // Restore font
        if ('' !== $fontFamily) {
            $this->setFont($fontFamily, $fontStyle, $fontSizeInPoint);
        }
        // Set colors
        $this->drawColor = $drawColor;
        if ('0 G' !== $drawColor) {
            $this->out($drawColor);
        }
        $this->fillColor = $fillColor;
        if ('0 G' !== $fillColor) {
            $this->out($fillColor);
        }
        $this->textColor = $textColor;
        $this->colorFlag = $colorFlag;

        // Page header
        $this->inHeader = true;
        $this->header();
        $this->inHeader = false;

        // Restore line width
        if ($this->lineWidth !== $lineWidth) {
            $this->lineWidth = $lineWidth;
            $this->outf('%.2F w', $lineWidth * $this->scaleFactor);
        }

        // Restore font
        if ('' !== $fontFamily) {
            $this->setFont($fontFamily, $fontStyle, $fontSizeInPoint);
        }

        // Restore colors
        if ($this->drawColor !== $drawColor) {
            $this->drawColor = $drawColor;
            $this->out($drawColor);
        }
        if ($this->fillColor !== $fillColor) {
            $this->fillColor = $fillColor;
            $this->out($fillColor);
        }
        $this->textColor = $textColor;
        $this->colorFlag = $colorFlag;

        return $this;
    }

    /**
     * @throws PdfException
     */
    public function cell(
        float $width = 0,
        float $height = 0,
        string $text = '',
        string|int $border = 0,
        PdfMove $move = PdfMove::RIGHT,
        PdfTextAlignment $align = PdfTextAlignment::LEFT,
        bool $fill = false,
        string|int $link = ''
    ): self {
        $scaleFactor = $this->scaleFactor;
        if ($this->y + $height > $this->pageBreakTrigger
            && !$this->inHeader && !$this->inFooter
            && $this->isAutoPageBreak()) {
            // Automatic page break
            $x = $this->x;
            $wordSpacing = $this->wordSpacing;
            if ($wordSpacing > 0) {
                $this->wordSpacing = 0;
                $this->out('0 Tw');
            }
            $this->addPage($this->currentOrientation, $this->currentPageSize, $this->currentRotation);
            $this->x = $x;
            if ($wordSpacing > 0) {
                $this->wordSpacing = $wordSpacing;
                $this->outf('%.3F Tw', $wordSpacing * $scaleFactor);
            }
        }
        if (0.0 === $width) {
            $width = $this->width - $this->rightMargin - $this->x;
        }
        $s = '';
        if ($fill || 1 === $border) {
            if ($fill) {
                $op = (1 === $border) ? 'B' : 'f';
            } else {
                $op = 'S';
            }
            $s = \sprintf(
                '%.2F %.2F %.2F %.2F re %s ',
                $this->x * $scaleFactor,
                ($this->height - $this->y) * $scaleFactor,
                $width * $scaleFactor,
                -$height * $scaleFactor,
                $op
            );
        }
        if (0 !== $border && \is_string($border)) {
            $x = $this->x;
            $y = $this->y;
            if (\str_contains($border, 'L')) {
                $s .= \sprintf(
                    '%.2F %.2F m %.2F %.2F l S ',
                    $x * $scaleFactor,
                    ($this->height - $y) * $scaleFactor,
                    $x * $scaleFactor,
                    ($this->height - ($y + $height)) * $scaleFactor
                );
            }
            if (\str_contains($border, 'T')) {
                $s .= \sprintf(
                    '%.2F %.2F m %.2F %.2F l S ',
                    $x * $scaleFactor,
                    ($this->height - $y) * $scaleFactor,
                    ($x + $width) * $scaleFactor,
                    ($this->height - $y) * $scaleFactor
                );
            }
            if (\str_contains($border, 'R')) {
                $s .= \sprintf(
                    '%.2F %.2F m %.2F %.2F l S ',
                    ($x + $width) * $scaleFactor,
                    ($this->height - $y) * $scaleFactor,
                    ($x + $width) * $scaleFactor,
                    ($this->height - ($y + $height)) * $scaleFactor
                );
            }
            if (\str_contains($border, 'B')) {
                $s .= \sprintf(
                    '%.2F %.2F m %.2F %.2F l S ',
                    $x * $scaleFactor,
                    ($this->height - ($y + $height)) * $scaleFactor,
                    ($x + $width) * $scaleFactor,
                    ($this->height - ($y + $height)) * $scaleFactor
                );
            }
        }
        if ('' !== $text) {
            if (null === $this->currentFont) {
                throw new PdfException('No font has been set.');
            }
            if (PdfTextAlignment::RIGHT === $align) {
                $dx = $width - $this->cellMargin - $this->getStringWidth($text);
            } elseif (PdfTextAlignment::CENTER === $align) {
                $dx = ($width - $this->getStringWidth($text)) / 2.0;
            } else {
                $dx = $this->cellMargin;
            }
            if ($this->colorFlag) {
                $s .= 'q ' . $this->textColor . ' ';
            }
            $s .= \sprintf(
                'BT %.2F %.2F Td (%s) Tj ET',
                ($this->x + $dx) * $scaleFactor,
                ($this->height - ($this->y + .5 * $height + .3 * $this->fontSize)) * $scaleFactor,
                $this->escape($text)
            );
            if ($this->underline) {
                $s .= ' ' . $this->doUnderline($this->x + $dx, $this->y + .5 * $height + .3 * $this->fontSize, $text);
            }
            if ($this->colorFlag) {
                $s .= ' Q';
            }
            if ('' !== $link) {
                $this->link($this->x + $dx, $this->y + .5 * $height - .5 * $this->fontSize, $this->getStringWidth($text), $this->fontSize, $link);
            }
        }
        if ('' !== $s) {
            $this->out($s);
        }
        $this->lastHeight = $height;
        switch ($move) {
            case PdfMove::RIGHT:
                $this->x += $width;
                break;
            case PdfMove::NEW_LINE:
                $this->y += $height;
                $this->x = $this->leftMargin;
                break;
            case PdfMove::BELOW:
                $this->y += $height;
                break;
        }

        return $this;
    }

    /**
     * @throws PdfException
     */
    public function close(): void
    {
        if (self::STATE_CLOSED === $this->state) {
            return;
        }
        if (0 === $this->page) {
            $this->addPage();
        }
        // Page footer
        $this->inFooter = true;
        $this->footer();
        $this->inFooter = false;
        // Close page
        $this->endPage();
        // Close document
        $this->endDoc();
    }

    /**
     * Creates a new internal link for the given position and page and returns its identifier.
     *
     * This is a combination of the <code>AddLink()</code> and the <code>SetLink()</code> functions.
     *
     * @param float|int $y    the ordinate of the target position; -1 means the current position. 0 means top of page.
     * @param int       $page the target page; -1 indicates the current page
     *
     * @return int the link identifier
     *
     * @see PdfDocument::AddLink()
     * @see PdfDocument::SetLink()
     */
    public function createLink(float|int $y = -1, int $page = -1): int
    {
        $id = $this->addLinK();
        $this->setLink($id, $y, $page);

        return $id;
    }

    public function footer(): void
    {
        // To be implemented in your own inherited class
    }

    /**
     * Gets the cell margin.
     *
     * The default value is 1 mm.
     */
    public function getCellMargin(): float
    {
        return $this->cellMargin;
    }

    /**
     * Gets the current font size in user unit.
     */
    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    /**
     * Gets the height of last printed cell.
     *
     * @return float the height or 0 if no printed cell
     */
    public function getLastHeight(): float
    {
        return $this->lastHeight;
    }

    /**
     * Gets the number of lines to use for the given text and width.
     *
     * Computes the number of lines a MultiCell of the given width will take.
     *
     * @param ?string $text       the text to compute
     * @param float   $width      the desired width. If 0, the width extends up to the right margin.
     * @param ?float  $cellMargin the desired cell margin or null to use current value
     *
     * @return int the number of lines
     *
     * @throws PdfException if no font is set
     */
    public function getLinesCount(?string $text, float $width = 0.0, ?float $cellMargin = null): int
    {
        if (null === $text || '' === $text) {
            return 0;
        }
        if (null === $this->currentFont) {
            throw new PdfException('No font has been set.');
        }
        $text = \rtrim(\str_replace("\r", '', $text));
        $len = \strlen($text);
        if (0 === $len) {
            return 0;
        }
        if ($width <= 0) {
            $width = $this->getRemainingWidth();
        }

        $sep = -1;
        $index = 0;
        $lastIndex = 0;
        $linesCount = 1;
        $currentWidth = 0.0;
        $cw = $this->currentFont['cw'];
        $cellMargin ??= $this->cellMargin;
        $maxWidth = ($width - 2.0 * $cellMargin) * 1000.0 / $this->fontSize;

        while ($index < $len) {
            $ch = $text[$index];
            // new line?
            if ("\n" === $ch) {
                ++$index;
                $sep = -1;
                $lastIndex = $index;
                $currentWidth = 0.0;
                ++$linesCount;
                continue;
            }
            if (' ' === $ch) {
                $sep = $index;
            }
            $currentWidth += $cw[$ch];
            if ($currentWidth > $maxWidth) {
                if (-1 === $sep) {
                    if ($index === $lastIndex) {
                        ++$index;
                    }
                } else {
                    $index = $sep + 1;
                }
                $sep = -1;
                $lastIndex = $index;
                $currentWidth = 0.0;
                ++$linesCount;
            } else {
                ++$index;
            }
        }

        return $linesCount;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageHeight(): float
    {
        return $this->height;
    }

    public function getPageWidth(): float
    {
        return $this->width;
    }

    /**
     * Gets the printable width.
     *
     * @return float the document width minus the left and right margins
     */
    public function getPrintableWidth(): float
    {
        return $this->width - $this->leftMargin - $this->rightMargin;
    }

    /**
     * Gets the remaining printable width.
     *
     * @return float the value from the current abscissa (x) to the right margin
     */
    public function getRemainingWidth(): float
    {
        return $this->width - $this->rightMargin - $this->x;
    }

    public function getStringWidth(string $str): float
    {
        if (null !== $this->currentFont) {
            $width = 0.0;
            $charWidths = $this->currentFont['cw'];
            for ($i = 0,$len = \strlen($str); $i < $len; ++$i) {
                $width += $charWidths[$str[$i]];
            }

            return $width * $this->fontSize / 1000.0;
        }

        return 0.0;
    }

    /**
     * Gets the current X position.
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * Gets the current X and Y position.
     *
     * @return float[] the X and Y position
     *
     * @psalm-return array{0: float, 1: float}
     *
     * @see PdfDocument::getX()
     * @see PdfDocument::getY()
     */
    public function getXY(): array
    {
        return [$this->x, $this->y];
    }

    /**
     * Gets the current Y position.
     */
    public function getY(): float
    {
        return $this->y;
    }

    public function header(): void
    {
        // To be implemented in your own inherited class
    }

    /**
     * @throws PdfException
     */
    public function image(
        string $file,
        ?float $x = null,
        ?float $y = null,
        float $width = 0.0,
        float $height = 0.0,
        string $type = '',
        string|int $link = ''
    ): self {
        if ('' === $file) {
            throw new PdfException('Image file name is empty.');
        }
        if (!isset($this->images[$file])) {
            // First use of this image, get info
            if ('' === $type) {
                $type = \pathinfo($file, \PATHINFO_EXTENSION);
                if ('' === $type) {
                    throw new PdfException(\sprintf('Image file has no extension and no type was specified: %s.', $file));
                }
            }
            $type = \strtolower($type);

            /** @phpstan-var PdfImageType $info */
            $info = match ($type) {
                'jpeg',
                'jpg' => $this->parseJpg($file),
                'gif' => $this->parseGif($file),
                'png' => $this->parsePng($file),
                default => throw new PdfException(\sprintf('Unsupported image type: %s.', $type)),
            };
            $info['i'] = \count($this->images) + 1;
            $this->images[$file] = $info;
        } else {
            $info = $this->images[$file];
        }

        // Automatic width and height calculation if needed
        if (0.0 === $width && 0.0 === $height) {
            // Put image at 96 dpi
            $width = -96.0;
            $height = -96.0;
        }

        $infoWidth = (float) $info['w'];
        $infoHeight = (float) $info['h'];
        if ($width < 0.0) {
            $width = -$infoWidth * 72.0 / $width / $this->scaleFactor;
        }
        if ($height < 0.0) {
            $height = -$infoHeight * 72.0 / $height / $this->scaleFactor;
        }
        if (0.0 === $width) {
            $width = $height * $infoWidth / $infoHeight;
        }
        if (0.0 === $height) {
            $height = $width * $infoHeight / $infoWidth;
        }

        // Flowing mode
        if (null === $y) {
            if ($this->y + $height > $this->pageBreakTrigger && !$this->inHeader && !$this->inFooter && $this->isAutoPageBreak()) {
                // Automatic page break
                $x2 = $this->x;
                $this->addPage($this->currentOrientation, $this->currentPageSize, $this->currentRotation);
                $this->x = $x2;
            }
            $y = $this->y;
            $this->y += $height;
        }

        if (null === $x) {
            $x = $this->x;
        }
        $this->outf(
            'q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q',
            $width * $this->scaleFactor,
            $height * $this->scaleFactor,
            $x * $this->scaleFactor,
            ($this->height - ($y + $height)) * $this->scaleFactor,
            $info['i']
        );
        if ('' !== $link && 0 !== $link) {
            $this->link($x, $y, $width, $height, $link);
        }

        return $this;
    }

    public function isAutoPageBreak(): bool
    {
        return $this->autoPageBreak;
    }

    /**
     * Returns if the given height would not cause an overflow (new page).
     *
     * @param float  $height the desired height
     * @param ?float $y      the ordinate position or null to use the current position
     *
     * @return bool true if printable within the current page; false if a new page is
     *              needed
     *
     * @see PdfDocument::AddPage()
     */
    public function isPrintable(float $height, ?float $y = null): bool
    {
        return (($y ?? $this->y) + $height) <= $this->pageBreakTrigger;
    }

    /**
     * @throws PdfException
     */
    public function line(float $x1, float $y1, float $x2, float $y2): self
    {
        $this->outf(
            '%.2F %.2F m %.2F %.2F l S',
            $x1 * $this->scaleFactor,
            ($this->height - $y1) * $this->scaleFactor,
            $x2 * $this->scaleFactor,
            ($this->height - $y2) * $this->scaleFactor
        );

        return $this;
    }

    public function lineFeed(?float $h = null): self
    {
        $this->x = $this->leftMargin;
        $this->y += $h ?? $this->lastHeight;

        return $this;
    }

    public function link(float $x, float $y, float $width, float $height, string|int $link): self
    {
        $this->pageLinks[$this->page][] = [
            $x * $this->scaleFactor,
            $this->heightInPoint - $y * $this->scaleFactor,
            $width * $this->scaleFactor,
            $height * $this->scaleFactor,
            $link,
            0,
        ];

        return $this;
    }

    /**
     * Sets the ordinate for the given delta value and optionally moves the current abscissa back to the left margin.
     *
     * @param float $delta  the delta value of the ordinate to move to
     * @param bool  $resetX whether to reset the abscissa
     */
    public function moveY(float $delta, bool $resetX = true): self
    {
        if (0.0 !== $delta) {
            $this->setY($this->getY() + $delta, $resetX);
        }

        return $this;
    }

    /**
     * @throws PdfException
     */
    public function multiCell(
        float $width,
        float $height,
        string $text,
        string|int $border = 0,
        PdfTextAlignment $align = PdfTextAlignment::JUSTIFIED,
        bool $fill = false
    ): self {
        if (null === $this->currentFont) {
            throw new PdfException('No font has been set.');
        }

        $charWidths = $this->currentFont['cw'];
        if (0.0 === $width) {
            $width = $this->width - $this->rightMargin - $this->x;
        }
        $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;
        $s = \str_replace("\r", '', $text);
        $len = \strlen($s);
        if ($len > 0 && "\n" === $s[$len - 1]) {
            --$len;
        }
        $border1 = 0;
        $border2 = '';
        if (1 === $border) {
            $border = 'LTRB';
            $border1 = 'LRT';
            $border2 = 'LR';
        } elseif (\is_string($border)) {
            if (\str_contains($border, 'L')) {
                $border2 .= 'L';
            }
            if (\str_contains($border, 'R')) {
                $border2 .= 'R';
            }
            $border1 = (\str_contains($border, 'T')) ? $border2 . 'T' : $border2;
        }
        $sep = -1;
        $index = 0;
        $j = 0;
        $l = 0.0;
        $ns = 0;
        $nl = 1;
        $ls = 0.0;
        while ($index < $len) {
            // Get next character
            $ch = $s[$index];
            if ("\n" === $ch) {
                // Explicit line break
                if ($this->wordSpacing > 0) {
                    $this->wordSpacing = 0;
                    $this->out('0 Tw');
                }
                $this->cell($width, $height, \substr($s, $j, $index - $j), $border1, PdfMove::BELOW, $align, $fill);
                ++$index;
                $sep = -1;
                $j = $index;
                $l = 0.0;
                $ns = 0;
                ++$nl;
                if (\is_string($border) && 2 === $nl) {
                    $border1 = $border2;
                }
                continue;
            }
            if (' ' === $ch) {
                $sep = $index;
                $ls = $l;
                ++$ns;
            }
            $l += $charWidths[$ch];
            if ($l > $widthMax) {
                // Automatic line break
                if (-1 === $sep) {
                    if ($index === $j) {
                        ++$index;
                    }
                    if ($this->wordSpacing > 0) {
                        $this->wordSpacing = 0;
                        $this->out('0 Tw');
                    }
                    $this->cell($width, $height, \substr($s, $j, $index - $j), $border1, PdfMove::BELOW, $align, $fill);
                } else {
                    if (PdfTextAlignment::JUSTIFIED === $align) {
                        $this->wordSpacing = ($ns > 1) ? ($widthMax - $ls) / 1000.0 * $this->fontSize / (float) ($ns - 1) : 0.0;
                        $this->outf('%.3F Tw', $this->wordSpacing * $this->scaleFactor);
                    }
                    $this->cell($width, $height, \substr($s, $j, $sep - $j), $border1, PdfMove::BELOW, $align, $fill);
                    $index = $sep + 1;
                }
                $sep = -1;
                $j = $index;
                $l = 0.0;
                $ns = 0;
                ++$nl;
                if (\is_string($border) && 2 === $nl) {
                    $border1 = $border2;
                }
            } else {
                ++$index;
            }
        }
        // Last chunk
        if ($this->wordSpacing > 0.0) {
            $this->wordSpacing = 0.0;
            $this->out('0 Tw');
        }
        if (0 !== $border && \is_string($border) && \str_contains($border, 'B') && \is_string($border1)) {
            $border1 .= 'B';
        }
        $this->cell($width, $height, \substr($s, $j, $index - $j), $border1, PdfMove::BELOW, $align, $fill);
        $this->x = $this->leftMargin;

        return $this;
    }

    /**
     * @throws PdfException
     */
    public function output(
        PdfDestination $destination = PdfDestination::INLINE,
        string $name = '',
        bool $isUTF8 = false
    ): string {
        $this->close();
        if ('' === $name) {
            $name = 'doc.pdf';
        }
        switch ($destination) {
            case PdfDestination::INLINE:
                // Send to standard output
                $this->checkOutput();
                if (\PHP_SAPI !== 'cli') {
                    // We send to a browser
                    \header('Content-Type: application/pdf');
                    \header('Content-Disposition: inline; ' . $this->httpEncode('filename', $name, $isUTF8));
                    \header('Cache-Control: private, max-age=0, must-revalidate');
                    \header('Pragma: public');
                }
                echo $this->buffer;
                break;
            case PdfDestination::DOWNLOAD:
                $this->checkOutput();
                \header('Content-Type: application/pdf');
                \header('Content-Disposition: attachment; ' . $this->httpEncode('filename', $name, $isUTF8));
                \header('Cache-Control: private, max-age=0, must-revalidate');
                \header('Pragma: public');
                echo $this->buffer;
                break;
            case PdfDestination::FILE:
                if (false === \file_put_contents($name, $this->buffer)) {
                    throw new PdfException(\sprintf('Unable to create output file: %s.', $name));
                }
                break;
            case PdfDestination::STRING:
                return $this->buffer;
        }

        return '';
    }

    /**
     * Converts the given pixels to millimeters using the given dot per each (DPI).
     *
     * @param float|int $pixels the pixels to convert
     * @param float     $dpi    the dot per inch
     *
     * @return float the converted value as millimeters
     *
     * @psalm-api
     */
    public function pixels2mm(float|int $pixels, float $dpi = 72): float
    {
        if (0.0 === $dpi) {
            return $pixels;
        }

        return (float) $pixels * 25.4 / $dpi;
    }

    /**
     * Converts the given pixels to user unit using 72 dot per each (DPI).
     *
     * @param float|int $pixels the pixels to convert
     *
     * @return float the converted value as user unit
     */
    public function pixels2UserUnit(float|int $pixels): float
    {
        return (float) $pixels * 72.0 / 96.0 / $this->scaleFactor;
    }

    /**
     * Converts the given points to user unit.
     */
    public function points2UserUnit(float|int $points): float
    {
        return (float) $points / $this->scaleFactor;
    }

    /**
     * @throws PdfException
     */
    public function rect(
        float $x,
        float $y,
        float $width,
        float $height,
        PdfRectangleStyle $style = PdfRectangleStyle::BORDER
    ): self {
        $this->outf(
            '%.2F %.2F %.2F %.2F re %s',
            $x * $this->scaleFactor,
            ($this->height - $y) * $this->scaleFactor,
            $width * $this->scaleFactor,
            -$height * $this->scaleFactor,
            $style->value
        );

        return $this;
    }

    public function setAliasNumberPages(string $aliasNumberPages = '{nb}'): self
    {
        $this->aliasNumberPages = $aliasNumberPages;

        return $this;
    }

    public function setAuthor(string $author, bool $isUTF8 = false): self
    {
        $this->addMetaData('Author', $author, $isUTF8);

        return $this;
    }

    public function setAutoPageBreak(bool $autoPageBreak, float $bottomMargin = 0): self
    {
        $this->autoPageBreak = $autoPageBreak;
        $this->bottomMargin = $bottomMargin;
        $this->pageBreakTrigger = $this->height - $bottomMargin;

        return $this;
    }

    /**
     * Sets the cell margin.
     */
    public function setCellMargin(float $margin): self
    {
        $this->cellMargin = \max(0.0, $margin);

        return $this;
    }

    public function setCompression(bool $compression): self
    {
        $this->compression = \function_exists('gzcompress') ? $compression : false;

        return $this;
    }

    public function setCreator(string $creator, bool $isUTF8 = false): self
    {
        $this->addMetaData('Creator', $creator, $isUTF8);

        return $this;
    }

    public function setDisplayMode(
        PdfZoom|int $zoom = PdfZoom::DEFAULT,
        PdfLayout $layout = PdfLayout::DEFAULT
    ): self {
        $this->zoom = $zoom;
        $this->layout = $layout;

        return $this;
    }

    /**
     * @throws PdfException
     */
    public function setDrawColor(int $r, ?int $g = null, ?int $b = null): self
    {
        $this->drawColor = $this->parseColor($r, $g, $b, true);
        if ($this->page > 0) {
            $this->out($this->drawColor);
        }

        return $this;
    }

    /**
     * @throws PdfException
     */
    public function setFillColor(int $r, ?int $g = null, ?int $b = null): self
    {
        $this->fillColor = $this->parseColor($r, $g, $b);
        $this->colorFlag = ($this->fillColor !== $this->textColor);
        if ($this->page > 0) {
            $this->out($this->fillColor);
        }

        return $this;
    }

    /**
     * @throws PdfException
     */
    public function setFont(string $family = '', PdfFontStyle $style = PdfFontStyle::REGULAR, float $size = 0): self
    {
        $family = '' === $family ? $this->fontFamily : \strtolower($family);
        $this->underline = $style->isUnderLine();
        if (0.0 === $size) {
            $size = $this->fontSizeInPoint;
        }
        // Test if font is already selected
        if ($this->fontFamily === $family && $this->fontStyle === $style && $this->fontSizeInPoint === $size) {
            return $this;
        }
        // Test if font is already loaded
        if ($this->underline) {
            $style = $style->removeUnderLine();
        }
        $fontKey = $family . $style->value;
        if (!isset($this->fonts[$fontKey])) {
            // Test if one of the core fonts
            if ('arial' === $family) {
                $family = 'helvetica';
            }
            if (!\in_array($family, self::CORE_FONTS, true)) {
                throw new PdfException(\sprintf('Undefined font: %s %s.' . $family, $style->value));
            }
            if ('symbol' === $family || 'zapfdingbats' === $family) {
                $style = PdfFontStyle::REGULAR;
            }
            $fontKey = $family . $style->value;
            if (!isset($this->fonts[$fontKey])) {
                $this->addFont($family, $style);
            }
        }
        // Select it
        $this->fontFamily = $family;
        $this->fontStyle = $style;
        $this->fontSizeInPoint = $size;
        $this->fontSize = $size / $this->scaleFactor;
        $this->currentFont = $this->fonts[$fontKey];
        if ($this->page > 0) {
            $this->outf('BT /F%d %.2F Tf ET', $this->currentFont['i'], $this->fontSizeInPoint);
        }

        return $this;
    }

    /**
     * @throws PdfException
     */
    public function setFontSize(float $size): self
    {
        if ($this->fontSizeInPoint === $size) {
            return $this;
        }
        $this->fontSizeInPoint = $size;
        $this->fontSize = $size / $this->scaleFactor;
        if ($this->page > 0 && null !== $this->currentFont) {
            $this->outf('BT /F%d %.2F Tf ET', $this->currentFont['i'], $this->fontSizeInPoint);
        }

        return $this;
    }

    public function setKeywords(string $keywords, bool $isUTF8 = false): self
    {
        $this->addMetaData('Keywords', $keywords, $isUTF8);

        return $this;
    }

    public function setLeftMargin(float $leftMargin): self
    {
        $this->leftMargin = $leftMargin;
        if ($this->page > 0 && $this->x < $leftMargin) {
            $this->x = $leftMargin;
        }

        return $this;
    }

    /**
     * @throws PdfException
     */
    public function setLineWidth(float $lineWidth): self
    {
        $this->lineWidth = $lineWidth;
        if ($this->page > 0) {
            $this->outf('%.2F w', $this->lineWidth * $this->scaleFactor);
        }

        return $this;
    }

    public function setLink(int $link, int|float $y = 0, int $page = -1): self
    {
        if (-1 === $y) {
            $y = $this->y;
        }
        if (-1 === $page) {
            $page = $this->page;
        }
        $this->links[$link] = [$page, $y];

        return $this;
    }

    public function setMargins(float $leftMargin, float $topMargin, ?float $rightMargin = null): self
    {
        $this->leftMargin = $leftMargin;
        $this->topMargin = $topMargin;
        if (null === $rightMargin) {
            $rightMargin = $leftMargin;
        }
        $this->rightMargin = $rightMargin;

        return $this;
    }

    public function setRightMargin(float $rightMargin): self
    {
        $this->rightMargin = $rightMargin;

        return $this;
    }

    public function setSubject(string $subject, bool $isUTF8 = false): self
    {
        $this->addMetaData('Subject', $subject, $isUTF8);

        return $this;
    }

    public function setTextColor(int $r, ?int $g = null, ?int $b = null): self
    {
        $this->textColor = $this->parseColor($r, $g, $b);
        $this->colorFlag = ($this->fillColor !== $this->textColor);

        return $this;
    }

    public function setTitle(string $title, bool $isUTF8 = false): self
    {
        $this->addMetaData('Title', $title, $isUTF8);

        return $this;
    }

    public function setTopMargin(float $topMargin): self
    {
        $this->topMargin = $topMargin;

        return $this;
    }

    public function setX(float $x): self
    {
        $this->x = $x >= 0 ? $x : $this->width + $x;

        return $this;
    }

    public function setXY(float $x, float $y): self
    {
        $this->SetX($x);
        $this->SetY($y, false);

        return $this;
    }

    public function setY(float $y, bool $resetX = true): self
    {
        $this->y = $y >= 0 ? $y : $this->height + $y;
        if ($resetX) {
            $this->x = $this->leftMargin;
        }

        return $this;
    }

    /**
     * @throws PdfException
     */
    public function text(float $x, float $y, string $text): self
    {
        if (null === $this->currentFont) {
            throw new PdfException('No font has been set.');
        }
        $s = \sprintf('BT %.2F %.2F Td (%s) Tj ET', $x * $this->scaleFactor, ($this->height - $y) * $this->scaleFactor, $this->escape($text));
        if ($this->underline && '' !== $text) {
            $s .= ' ' . $this->doUnderline($x, $y, $text);
        }
        if ($this->colorFlag) {
            $s = 'q ' . $this->textColor . ' ' . $s . ' Q';
        }
        $this->out($s);

        return $this;
    }

    /**
     * Ensure that this version is equal to or greater than the given version.
     *
     * @param string $version the minimum version to set
     */
    public function updateVersion(string $version): void
    {
        if (\version_compare($this->pdfVersion, $version, '<')) {
            $this->pdfVersion = $version;
        }
    }

    /**
     * Set the cell margins to the given value, call the given user function and reset margins to previous value.
     */
    public function useCellMargin(callable $callable, float $margin = 0.0): self
    {
        $previousMargin = $this->getCellMargin();
        $this->setCellMargin($margin);
        \call_user_func($callable);
        $this->setCellMargin($previousMargin);

        return $this;
    }

    /**
     * @throws PdfException
     */
    public function write(float $h, string $text, string|int $link = ''): self
    {
        if (null === $this->currentFont) {
            throw new PdfException('No font has been set.');
        }
        $charWidths = $this->currentFont['cw'];
        $width = $this->width - $this->rightMargin - $this->x;
        $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;
        $s = \str_replace("\r", '', $text);
        $nb = \strlen($s);
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0.0;
        $nl = 1;
        while ($i < $nb) {
            // Get next character
            $c = $s[$i];
            if ("\n" === $c) {
                // Explicit line break
                $this->cell($width, $h, \substr($s, $j, $i - $j), 0, PdfMove::BELOW, PdfTextAlignment::LEFT, false, $link);
                ++$i;
                $sep = -1;
                $j = $i;
                $l = 0.0;
                if (1 === $nl) {
                    $this->x = $this->leftMargin;
                    $width = $this->width - $this->rightMargin - $this->x;
                    $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;
                }
                ++$nl;
                continue;
            }
            if (' ' === $c) {
                $sep = $i;
            }
            $l += $charWidths[$c];
            if ($l > $widthMax) {
                // Automatic line break
                if (-1 === $sep) {
                    if ($this->x > $this->leftMargin) {
                        // Move to next line
                        $this->x = $this->leftMargin;
                        $this->y += $h;
                        $width = $this->width - $this->rightMargin - $this->x;
                        $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;
                        ++$i;
                        ++$nl;
                        continue;
                    }
                    if ($i === $j) {
                        ++$i;
                    }
                    $this->cell($width, $h, \substr($s, $j, $i - $j), 0, PdfMove::BELOW, PdfTextAlignment::LEFT, false, $link);
                } else {
                    $this->cell($width, $h, \substr($s, $j, $sep - $j), 0, PdfMove::BELOW, PdfTextAlignment::LEFT, false, $link);
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0.0;
                if (1 === $nl) {
                    $this->x = $this->leftMargin;
                    $width = $this->width - $this->rightMargin - $this->x;
                    $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;
                }
                ++$nl;
            } else {
                ++$i;
            }
        }
        // Last chunk
        if ($i !== $j) {
            $this->cell($l / 1000.0 * $this->fontSize, $h, \substr($s, $j), 0, PdfMove::RIGHT, PdfTextAlignment::LEFT, false, $link);
        }

        return $this;
    }

    protected function addMetaData(string $key, string $value, bool $isUTF8 = false): void
    {
        $this->metadata[$key] = $isUTF8 ? $value : $this->utf8encode($value);
    }

    /**
     * @param PdfSize|PdfPageSizeType|null $size
     *
     * @throws PdfException
     */
    protected function beginPage(
        ?PdfOrientation $orientation = null,
        PdfSize|array|null $size = null,
        int $rotation = 0
    ): void {
        ++$this->page;
        $this->pages[$this->page] = '';
        $this->pageLinks[$this->page] = [];
        $this->state = self::STATE_PAGE_STARTED;
        $this->x = $this->leftMargin;
        $this->y = $this->topMargin;
        $this->fontFamily = '';
        // Check page size and orientation
        if (!$orientation instanceof PdfOrientation) {
            $orientation = $this->defaultOrientation;
        }
        $size = null === $size ? $this->defaultPageSize : $this->getPageSize($size);
        if ($orientation !== $this->currentOrientation
            || $size[0] !== $this->currentPageSize[0]
            || $size[1] !== $this->currentPageSize[1]) {
            // New size or orientation
            if (PdfOrientation::PORTRAIT === $orientation) {
                $this->width = $size[0];
                $this->height = $size[1];
            } else {
                $this->width = $size[1];
                $this->height = $size[0];
            }
            $this->widthInPoint = $this->width * $this->scaleFactor;
            $this->heightInPoint = $this->height * $this->scaleFactor;
            $this->pageBreakTrigger = $this->height - $this->bottomMargin;
            $this->currentOrientation = $orientation;
            $this->currentPageSize = $size;
        }
        if ($orientation !== $this->defaultOrientation
            || $size[0] !== $this->defaultPageSize[0]
            || $size[1] !== $this->defaultPageSize[1]) {
            $this->pageInfos[$this->page]['size'] = [$this->widthInPoint, $this->heightInPoint];
        }
        if (0 !== $rotation) {
            if (0 !== $rotation % 90) {
                throw new PdfException(\sprintf('Incorrect rotation value: %d.', $rotation));
            }
            $this->pageInfos[$this->page]['rotation'] = $rotation;
        }
        $this->currentRotation = $rotation;
    }

    /**
     * @throws PdfException
     */
    protected function checkOutput(): void
    {
        if (\PHP_SAPI !== 'cli' && \headers_sent($file, $line)) {
            throw new PdfException(\sprintf('Some data has already been output, can not send PDF file (output started at %s:%s).', $file, $line));
        }
        if (false !== \ob_get_length()) {
            // The output buffer is not empty
            $content = \ob_get_contents();
            if (\is_string($content) && \preg_match('/^(\xEF\xBB\xBF)?\s*$/', $content)) {
                // It contains only a UTF-8 BOM and/or whitespace, let's clean it
                \ob_clean();
            } else {
                throw new PdfException('Some data has already been output, can not send PDF file.');
            }
        }
    }

    /**
     * @throws PdfException
     */
    protected function doUnderline(float $x, float $y, string $txt): string
    {
        if (null === $this->currentFont) {
            throw new PdfException('No font has been set.');
        }
        // Underline text
        $up = $this->currentFont['up'];
        $ut = $this->currentFont['ut'];
        $width = $this->getStringWidth($txt) + $this->wordSpacing * (float) \substr_count($txt, ' ');

        return \sprintf(
            '%.2F %.2F %.2F %.2F re f',
            $x * $this->scaleFactor,
            ($this->height - ($y - $up / 1000.0 * $this->fontSize)) * $this->scaleFactor,
            $width * $this->scaleFactor,
            -$ut / 1000.0 * $this->fontSizeInPoint
        );
    }

    /**
     * @throws PdfException
     */
    protected function endDoc(): void
    {
        $this->creationDate = \time();
        $this->putHeader();
        $this->putPages();
        $this->putResources();
        // Info
        $this->newObj();
        $this->put('<<');
        $this->putInfo();
        $this->put('>>');
        $this->endObj();
        // Catalog
        $this->newObj();
        $this->put('<<');
        $this->putCatalog();
        $this->put('>>');
        $this->endObj();
        // Cross-ref
        $offset = $this->getOffset();
        $this->put('xref');
        $this->putf('0 %d', $this->objectNumber + 1);
        $this->put('0000000000 65535 f ');
        for ($i = 1; $i <= $this->objectNumber; ++$i) {
            $this->putf('%010d 00000 n ', $this->offsets[$i]);
        }
        // Trailer
        $this->put('trailer');
        $this->put('<<');
        $this->putTrailer();
        $this->put('>>');
        $this->put('startxref');
        $this->put($offset);
        $this->put('%%EOF');
        $this->state = self::STATE_CLOSED;
    }

    protected function endObj(): void
    {
        $this->put('endobj');
    }

    protected function endPage(): void
    {
        $this->state = self::STATE_END_PAGE;
    }

    protected function escape(string $str): string
    {
        // Escape special characters
        if (\str_contains($str, '(') || \str_contains($str, ')') || \str_contains($str, '\\') || \str_contains($str, "\r")) {
            return \str_replace(['\\', '(', ')', "\r"], ['\\\\', '\\(', '\\)', '\\r'], $str);
        }

        return $str;
    }

    protected function getOffset(): int
    {
        return \strlen($this->buffer);
    }

    /**
     * @param PdfSize|PdfPageSizeType $size
     *
     * @return PdfPageSizeType
     */
    protected function getPageSize(PdfSize|array $size): array
    {
        if ($size instanceof PdfSize) {
            $size = $size->getSize();

            return [$size[0] / $this->scaleFactor, $size[1] / $this->scaleFactor];
        }
        if ($size[0] > $size[1]) {
            return [$size[1], $size[0]];
        }

        return $size;
    }

    protected function httpEncode(string $param, string $value, bool $isUTF8): string
    {
        // Encode HTTP header field parameter
        if ($this->isAscii($value)) {
            return $param . '="' . $value . '"';
        }
        if (!$isUTF8) {
            $value = $this->utf8encode($value);
        }

        return $param . "*=UTF-8''" . \rawurlencode($value);
    }

    protected function isAscii(string $s): bool
    {
        // Test if string is ASCII
        $nb = \strlen($s);
        for ($i = 0; $i < $nb; ++$i) {
            if (\ord($s[$i]) > 127) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws PdfException
     *
     * @psalm-suppress UndefinedVariable
     * @psalm-suppress UnusedVariable
     * @psalm-suppress UnresolvableInclude
     */
    protected function loadFont(string $path): array
    {
        // Load a font definition file
        include $path;
        if (!isset($name)) {
            throw new PdfException(\sprintf('Could not include font definition file: %s.', $path));
        }
        if (isset($enc)) {
            $enc = \strtolower((string) $enc);
        }
        if (!isset($subsetted)) {
            $subsetted = false;
        }

        return \get_defined_vars();
    }

    protected function newObj(?int $n = null): void
    {
        // Begin a new object
        if (null === $n) {
            $n = ++$this->objectNumber;
        }
        $this->offsets[$n] = $this->getOffset();
        $this->putf('%d 0 obj', $n);
    }

    /**
     * @throws PdfException
     */
    protected function out(string $s): void
    {
        // Add a line to the current page
        switch ($this->state) {
            case self::STATE_NO_PAGE:
                throw new PdfException('No page has been added yet.');
            case self::STATE_END_PAGE:
                throw new PdfException('Invalid call (end page).');
            case self::STATE_CLOSED:
                throw new PdfException('The document is closed.');
            case self::STATE_PAGE_STARTED:
                $this->pages[$this->page] .= $s . "\n";
                break;
        }
    }

    /**
     * @throws PdfException
     */
    protected function outf(string $format, string|int|float ...$values): void
    {
        $this->out(\sprintf($format, ...$values));
    }

    protected function parseColor(int $r, ?int $g = null, ?int $b = null, bool $upper = false): string
    {
        if ((0 === $r && 0 === $g && 0 === $b) || null === $g) {
            $suffix = $upper ? 'G' : 'g';

            return \sprintf('%.3F %s', (float) $r / 255.0, $suffix);
        }
        $suffix = $upper ? 'RG' : 'rg';

        return \sprintf('%.3F %.3F %.3F %s', (float) $r / 255.0, (float) $g / 255.0, (float) $b / 255.0, $suffix);
    }

    /**
     * @throws PdfException
     */
    protected function parseGif(string $file): array
    {
        // Extract info from a GIF file (via PNG conversion)
        if (!\function_exists('imagepng')) {
            throw new PdfException('GD extension is required for GIF support.');
        }
        if (!\function_exists('imagecreatefromgif')) {
            throw new PdfException('GD has no GIF read support.');
        }
        /** @phpstan-var \GdImage $image */
        $image = \imagecreatefromgif($file);
        if (!$image instanceof \GdImage) {
            throw new PdfException(\sprintf('Missing or incorrect image file: %s.', $file));
        }
        \imageinterlace($image, false);
        \ob_start();
        \imagepng($image);
        $data = (string) \ob_get_clean();
        \imagedestroy($image);
        $f = \fopen('php://temp', 'rb+');
        if (!\is_resource($f)) {
            throw new PdfException('Unable to create memory stream.');
        }

        try {
            \fwrite($f, $data);
            \rewind($f);

            return $this->parsePngStream($f, $file);
        } finally {
            \fclose($f);
        }
    }

    /**
     * @throws PdfException
     */
    protected function parseJpg(string $file): array
    {
        // Extract info from a JPEG file
        $size = \getimagesize($file);
        if (!\is_array($size)) {
            throw new PdfException(\sprintf('Missing or incorrect image file: %s.', $file));
        }
        /** @phpstan-var array $size */
        if (2 !== $size[2]) {
            throw new PdfException(\sprintf('The file is not a JPEG image: %s.', $file));
        }
        if (!isset($size['channels']) || 3 === $size['channels']) {
            $color_space = 'DeviceRGB';
        } elseif (4 === $size['channels']) {
            $color_space = 'DeviceCMYK';
        } else {
            $color_space = 'DeviceGray';
        }
        /** @var int $bpc */
        $bpc = $size['bits'] ?? 8;
        $data = \file_get_contents($file);
        if (false === $data) {
            throw new PdfException(\sprintf('Unable get image file content: %s.', $file));
        }

        return ['w' => $size[0], 'h' => $size[1], 'cs' => $color_space, 'bpc' => $bpc, 'f' => 'DCTDecode', 'data' => $data];
    }

    /**
     * @throws PdfException
     */
    protected function parsePng(string $file): array
    {
        // Extract info from a PNG file
        $f = \fopen($file, 'r');
        if (!\is_resource($f)) {
            throw new PdfException(\sprintf('Can not open image file: %s.', $file));
        }

        try {
            return $this->parsePngStream($f, $file);
        } finally {
            \fclose($f);
        }
    }

    /**
     * @param resource $f
     *
     * @throws PdfException
     */
    protected function parsePngStream($f, string $file): array
    {
        // Check signature
        if ($this->readStream($f, 8) !== \chr(137) . 'PNG' . \chr(13) . \chr(10) . \chr(26) . \chr(10)) {
            throw new PdfException(\sprintf('File is not a PNG image: %s.', $file));
        }

        // Read header chunk
        $this->readStream($f, 4);
        if ('IHDR' !== $this->readStream($f, 4)) {
            throw new PdfException(\sprintf('Incorrect PNG file: %s.', $file));
        }
        $w = $this->readInt($f);
        $h = $this->readInt($f);
        $bpc = \ord($this->readStream($f, 1));
        if ($bpc > 8) {
            throw new PdfException(\sprintf('16-bit depth not supported: %s.', $file));
        }
        $ct = \ord($this->readStream($f, 1));
        if (0 === $ct || 4 === $ct) {
            $color_space = 'DeviceGray';
        } elseif (2 === $ct || 6 === $ct) {
            $color_space = 'DeviceRGB';
        } elseif (3 === $ct) {
            $color_space = 'Indexed';
        } else {
            throw new PdfException(\sprintf('Unknown color type: %s.', $file));
        }
        if (0 !== \ord($this->readStream($f, 1))) {
            throw new PdfException(\sprintf('Unknown compression method: %s.', $file));
        }
        // @phpstan-ignore-next-line
        if (0 !== \ord($this->readStream($f, 1))) {
            throw new PdfException(\sprintf('Unknown filter method: %s.', $file));
        }
        // @phpstan-ignore-next-line
        if (0 !== \ord($this->readStream($f, 1))) {
            throw new PdfException(\sprintf('Interlacing not supported: %s.', $file));
        }
        $this->readStream($f, 4);
        $dp = \sprintf(
            '/Predictor 15 /Colors %d /BitsPerComponent %d /Columns %d',
            'DeviceRGB' === $color_space ? 3 : 1,
            $bpc,
            $w
        );

        // Scan chunks looking for the palette, transparency and image data
        $pal = '';
        $trns = '';
        $data = '';
        do {
            $n = $this->readInt($f);
            $type = $this->readStream($f, 4);
            switch ($type) {
                case 'PLTE':
                    // Read palette
                    $pal = $this->readStream($f, $n);
                    $this->readStream($f, 4);
                    break;
                case 'tRNS':
                    // Read transparency info
                    $t = $this->readStream($f, $n);
                    if (0 === $ct) {
                        $trns = [\ord(\substr($t, 1, 1))];
                    } elseif (2 === $ct) {
                        $trns = [\ord(\substr($t, 1, 1)), \ord(\substr($t, 3, 1)), \ord(\substr($t, 5, 1))];
                    } else {
                        $pos = \strpos($t, \chr(0));
                        if (false !== $pos) {
                            $trns = [$pos];
                        }
                    }
                    $this->readStream($f, 4);
                    break;
                case 'IDAT':
                    // Read image data block
                    $data .= $this->readStream($f, $n);
                    $this->readStream($f, 4);
                    break;
                default:
                    $this->readStream($f, $n + 4);
                    break;
            }
        } while ($n);

        if ('Indexed' === $color_space && '' === $pal) {
            throw new PdfException(\sprintf('Missing palette in %s.', $file));
        }
        $info = [
            'w' => $w,
            'h' => $h,
            'cs' => $color_space,
            'bpc' => $bpc,
            'f' => 'FlateDecode',
            'dp' => $dp,
            'pal' => $pal,
            'trns' => $trns,
        ];
        if ($ct >= 4) {
            // Extract alpha channel
            if (!\function_exists('gzuncompress')) {
                throw new PdfException(\sprintf('Zlib not available, can not handle alpha channel: %s.', $file));
            }
            /** @phpstan-var non-empty-string $data */
            $data = \gzuncompress($data);
            $color = '';
            $alpha = '';
            if (4 === $ct) {
                // Gray image
                $len = 2 * $w;
                for ($i = 0; $i < $h; ++$i) {
                    $pos = (1 + $len) * $i;
                    $color .= $data[$pos];
                    $alpha .= $data[$pos];
                    $line = \substr($data, $pos + 1, $len);
                    $color .= \preg_replace('/(.)./s', '$1', $line);
                    $alpha .= \preg_replace('/.(.)/s', '$1', $line);
                }
            } else {
                // RGB image
                $len = 4 * $w;
                for ($i = 0; $i < $h; ++$i) {
                    $pos = (1 + $len) * $i;
                    $color .= $data[$pos];
                    $alpha .= $data[$pos];
                    $line = \substr($data, $pos + 1, $len);
                    $color .= \preg_replace('/(.{3})./s', '$1', $line);
                    $alpha .= \preg_replace('/.{3}(.)/s', '$1', $line);
                }
            }
            unset($data);
            $data = \gzcompress($color);
            $info['smask'] = \gzcompress($alpha);
            $this->withAlpha = true;
            if ($this->pdfVersion < '1.4') {
                $this->pdfVersion = '1.4';
            }
        }
        $info['data'] = $data;

        return $info;
    }

    protected function put(string|int $s): void
    {
        $this->buffer .= (string) $s . "\n";
    }

    protected function putCatalog(): void
    {
        $n = $this->pageInfos[1]['n'];
        $this->put('/Type /Catalog');
        $this->put('/Pages 1 0 R');
        if ($this->zoom instanceof PdfZoom) {
            switch ($this->zoom) {
                case PdfZoom::FULL_PAGE:
                    $this->putf('/OpenAction [%d 0 R /Fit]', $n);
                    break;
                case PdfZoom::FULL_WIDTH:
                    $this->putf('/OpenAction [%d 0 R /FitH null]', $n);
                    break;
                case PdfZoom::REAL:
                    $this->putf('/OpenAction [%d 0 R /XYZ null null 1]', $n);
                    break;
                default:
                    break;
            }
        } else {
            $this->putf('/OpenAction [%d 0 R /XYZ null null %.2F]', $n, (float) $this->zoom / 100.0);
        }

        switch ($this->layout) {
            case PdfLayout::SINGLE:
                $this->put('/PageLayout /SinglePage');
                break;
            case PdfLayout::CONTINUOUS:
                $this->put('/PageLayout /OneColumn');
                break;
            case PdfLayout::TWO_PAGES:
                $this->put('/PageLayout /TwoColumnLeft');
                break;
            default:
                break;
        }
    }

    protected function putf(string $format, string|int|float ...$values): void
    {
        $this->put(\sprintf($format, ...$values));
    }

    /**
     * @throws PdfException
     */
    protected function putFonts(): void
    {
        foreach ($this->fontFiles as $file => $info) {
            // Font file embedding
            $this->newObj();
            $this->fontFiles[$file]['n'] = $this->objectNumber;
            $content = \file_get_contents($file);
            if (false === $content) {
                throw new PdfException(\sprintf('Font file not found: %s.', $file));
            }
            $compressed = \str_ends_with($file, '.z');
            if (!$compressed && isset($info['length2'])) {
                $length1 = (int) $info['length1'];
                $length2 = (int) $info['length2'];
                $content = \substr($content, 6, $length1) . \substr($content, 6 + $length1 + 6, $length2);
            }
            $this->putf('<</Length %d', \strlen($content));
            if ($compressed) {
                $this->put('/Filter /FlateDecode');
            }
            $this->putf('/Length1 %d', (int) $info['length1']);
            if (isset($info['length2'])) {
                $this->putf('/Length2 %d /Length3 0', (int) $info['length2']);
            }
            $this->put('>>');
            $this->putStream($content);
            $this->endObj();
        }

        foreach ($this->fonts as $k => $font) {
            // Encoding
            if (isset($font['diff'])) {
                $enc = $font['enc'] ?? '';
                if (!isset($this->encodings[$enc])) {
                    $this->newObj();
                    $this->put('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [' . $font['diff'] . ']>>');
                    $this->endObj();
                    $this->encodings[$enc] = $this->objectNumber;
                }
            }
            // ToUnicode CMap
            $cmapkey = '';
            if (isset($font['uv'])) {
                $cmapkey = $font['enc'] ?? $font['name'];
                if (!isset($this->cMaps[$cmapkey])) {
                    $cmap = $this->toUnicodeCmap($font['uv']);
                    $this->putStreamObject($cmap);
                    $this->cMaps[$cmapkey] = $this->objectNumber;
                }
            }
            // Font object
            $this->fonts[$k]['n'] = $this->objectNumber + 1;
            $type = $font['type'];
            $name = $font['name'];
            if ($font['subsetted']) {
                $name = 'AAAAAA+' . $name;
            }
            if ('Core' === $type) {
                // Core font
                $this->newObj();
                $this->put('<</Type /Font');
                $this->put('/BaseFont /' . $name);
                $this->put('/Subtype /Type1');
                if ('Symbol' !== $name && 'ZapfDingbats' !== $name) {
                    $this->put('/Encoding /WinAnsiEncoding');
                }
                if (isset($font['uv'])) {
                    $this->putf('/ToUnicode %d 0 R', $this->cMaps[$cmapkey]);
                }
                $this->put('>>');
                $this->endObj();
            } elseif ('Type1' === $type || 'TrueType' === $type) {
                // Additional Type1 or TrueType/OpenType font
                $this->newObj();
                $this->put('<</Type /Font');
                $this->put('/BaseFont /' . $name);
                $this->put('/Subtype /' . $type);
                $this->put('/FirstChar 32 /LastChar 255');
                $this->putf('/Widths %d 0 R', $this->objectNumber + 1);
                $this->putf('/FontDescriptor %d 0 R', $this->objectNumber + 2);
                if (isset($font['diff']) && isset($font['enc'])) {
                    $this->putf('/Encoding %d 0 R', $this->encodings[$font['enc']]);
                } else {
                    $this->put('/Encoding /WinAnsiEncoding');
                }
                if (isset($font['uv'])) {
                    $this->putf('/ToUnicode %d 0 R', $this->cMaps[$cmapkey]);
                }
                $this->put('>>');
                $this->endObj();
                // Widths
                $this->newObj();
                $charWidths = $font['cw'];
                $s = '[';
                for ($i = 32; $i <= 255; ++$i) {
                    $s .= (string) $charWidths[\chr($i)] . ' ';
                }
                $s .= ']';
                $this->put($s);
                $this->endObj();
                // Descriptor
                $this->newObj();
                $s = '<</Type /FontDescriptor /FontName /' . $name;
                foreach ($font['desc'] as $key => $value) {
                    $s .= ' /' . $key . ' ' . $value;
                }
                if (!empty($font['file'])) {
                    $s .= \sprintf(' /FontFile %s %d 0 R', 'Type1' === $type ? '' : '2', (int) $this->fontFiles[$font['file']]['n']);
                }
                $this->put($s . '>>');
                $this->endObj();
            } else {
                // Allow for additional types
                $mtd = '_put' . \strtolower($type);
                if (!\method_exists($this, $mtd)) {
                    throw new PdfException(\sprintf('Unsupported font type: %s.', $type));
                }
                $this->$mtd($font);
            }
        }
    }

    protected function putHeader(): void
    {
        $this->put('%PDF-' . $this->pdfVersion);
    }

    /**
     * @phpstan-param PdfImageType $info
     */
    protected function putImage(array &$info): void
    {
        $this->newObj();
        $info['n'] = $this->objectNumber;
        $this->put('<</Type /XObject');
        $this->put('/Subtype /Image');
        $this->putf('/Width %d', $info['w']);
        $this->putf('/Height %d', $info['h']);
        if ('Indexed' === $info['cs']) {
            $this->putf('/ColorSpace [/Indexed /DeviceRGB %d %d 0 R]', \intdiv(\strlen($info['pal']), 3) - 1, $this->objectNumber + 1);
        } else {
            $this->put('/ColorSpace /' . $info['cs']);
            if ('DeviceCMYK' === $info['cs']) {
                $this->put('/Decode [1 0 1 0 1 0 1 0]');
            }
        }
        $this->putf('/BitsPerComponent %d', $info['bpc']);
        if (isset($info['f'])) {
            $this->put('/Filter /' . $info['f']);
        }
        if (isset($info['dp'])) {
            $this->put('/DecodeParms <<' . $info['dp'] . '>>');
        }
        if (isset($info['trns']) && \is_array($info['trns'])) {
            $trns = '';
            for ($i = 0, $counter = \count($info['trns']); $i < $counter; ++$i) {
                $trns .= $info['trns'][$i] . ' ' . $info['trns'][$i] . ' ';
            }
            $this->put('/Mask [' . $trns . ']');
        }
        if (isset($info['smask'])) {
            $this->putf('/SMask %d 0 R', $this->objectNumber + 1);
        }
        $this->putf('/Length %d>>', \strlen($info['data']));
        $this->putStream($info['data']);
        $this->endObj();
        // Soft mask
        if (isset($info['smask'])) {
            $dp = \sprintf('/Predictor 15 /Colors 1 /BitsPerComponent 8 /Columns %.2f', $info['w']);
            /** @phpstan-var PdfImageType $smask */
            $smask = [
                'w' => $info['w'],
                'h' => $info['h'],
                'cs' => 'DeviceGray',
                'bpc' => 8,
                'f' => $info['f'] ?? '',
                'dp' => $dp,
                'data' => $info['smask'],
            ];
            $this->putImage($smask);
        }
        // Palette
        if ('Indexed' === $info['cs']) {
            $this->putStreamObject($info['pal']);
        }
    }

    protected function putImages(): void
    {
        foreach (\array_keys($this->images) as $file) {
            $this->putImage($this->images[$file]);
            unset($this->images[$file]['data']);
            unset($this->images[$file]['smask']);
        }
    }

    protected function putInfo(): void
    {
        $date = \date('YmdHisO', $this->creationDate);
        $value = \sprintf("D:%s'%s'", \substr($date, 0, -2), \substr($date, -2));
        $this->addMetaData('CreationDate', $value);
        foreach ($this->metadata as $key => $value) {
            $this->put('/' . $key . ' ' . $this->textString($value));
        }
    }

    protected function putLinks(int $n): void
    {
        foreach ($this->pageLinks[$n] as $pageLink) {
            $this->newObj();
            $rect = \sprintf(
                '%.2F %.2F %.2F %.2F',
                $pageLink[0],
                $pageLink[1],
                $pageLink[0] + $pageLink[2],
                $pageLink[1] - $pageLink[3]
            );
            $s = '<</Type /Annot /Subtype /Link /Rect [' . $rect . '] /Border [0 0 0] ';
            if (\is_string($pageLink[4])) {
                $s .= '/A <</S /URI /URI ' . $this->textString($pageLink[4]) . '>>>>';
            } else {
                $link = $this->links[$pageLink[4]];
                $index = (int) $link[0];
                $pageInfo = $this->pageInfos[$index];
                if (isset($pageInfo['size'])) {
                    $height = $pageInfo['size'][1];
                } else {
                    $height = (PdfOrientation::PORTRAIT === $this->defaultOrientation)
                        ? $this->defaultPageSize[1] * $this->scaleFactor
                        : $this->defaultPageSize[0] * $this->scaleFactor;
                }
                $s .= \sprintf(
                    '/Dest [%d 0 R /XYZ 0 %.2F null]>>',
                    $pageInfo['n'],
                    $height - (float) $link[1] * $this->scaleFactor
                );
            }
            $this->put($s);
            $this->endObj();
        }
    }

    protected function putPage(int $n): void
    {
        $this->newObj();
        $this->put('<</Type /Page');
        $this->put('/Parent 1 0 R');
        if (isset($this->pageInfos[$n]['size'])) {
            $this->putf('/MediaBox [0 0 %.2F %.2F]', $this->pageInfos[$n]['size'][0], $this->pageInfos[$n]['size'][1]);
        }
        if (isset($this->pageInfos[$n]['rotation'])) {
            $this->putf('/Rotate %d', $this->pageInfos[$n]['rotation']);
        }
        $this->put('/Resources 2 0 R');
        if (!empty($this->pageLinks[$n])) {
            $s = '/Annots [';
            foreach ($this->pageLinks[$n] as $pageLink) {
                $s .= \sprintf('%d 0 R ', $pageLink[5]);
            }
            $s .= ']';
            $this->put($s);
        }
        if ($this->withAlpha) {
            $this->put('/Group <</Type /Group /S /Transparency /CS /DeviceRGB>>');
        }
        $this->putf('/Contents %d 0 R>>', $this->objectNumber + 1);
        $this->endObj();
        // Page content
        if (!empty($this->aliasNumberPages)) {
            $this->pages[$n] = \str_replace($this->aliasNumberPages, (string) $this->page, $this->pages[$n]);
        }
        $this->putStreamObject($this->pages[$n]);
        // Link annotations
        $this->putLinks($n);
    }

    /**
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    protected function putPages(): void
    {
        $nb = $this->page;
        $n = $this->objectNumber;
        for ($i = 1; $i <= $nb; ++$i) {
            $this->pageInfos[$i]['n'] = ++$n;
            ++$n;
            foreach ($this->pageLinks[$i] as &$pl) {
                $pl[5] = ++$n;
            }
        }
        for ($i = 1; $i <= $nb; ++$i) {
            $this->putPage($i);
        }
        // Pages root
        $this->newObj(1);
        $this->put('<</Type /Pages');
        $kids = '/Kids [';
        for ($i = 1; $i <= $nb; ++$i) {
            $kids .= \sprintf('%d 0 R ', (string) $this->pageInfos[$i]['n']);
        }
        $kids .= ']';
        $this->put($kids);
        $this->putf('/Count %d', $nb);
        if (PdfOrientation::PORTRAIT === $this->defaultOrientation) {
            $w = $this->defaultPageSize[0];
            $h = $this->defaultPageSize[1];
        } else {
            $w = $this->defaultPageSize[1];
            $h = $this->defaultPageSize[0];
        }
        $this->put(\sprintf('/MediaBox [0 0 %.2F %.2F]', $w * $this->scaleFactor, $h * $this->scaleFactor));
        $this->put('>>');
        $this->endObj();
    }

    protected function putResourceDictionary(): void
    {
        $this->put('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
        $this->put('/Font <<');
        foreach ($this->fonts as $font) {
            $this->putf('/F%d %d 0 R', $font['i'], $font['n']);
        }
        $this->put('>>');
        $this->put('/XObject <<');
        $this->putXObjectDictionary();
        $this->put('>>');
    }

    /**
     * @throws PdfException
     */
    protected function putResources(): void
    {
        $this->putFonts();
        $this->putImages();
        // Resource dictionary
        $this->newObj(2);
        $this->put('<<');
        $this->putResourceDictionary();
        $this->put('>>');
        $this->endObj();
    }

    protected function putStream(string $data): void
    {
        $this->put('stream');
        $this->put($data);
        $this->put('endstream');
    }

    protected function putStreamObject(string $data): void
    {
        if ($this->compression) {
            $entries = '/Filter /FlateDecode ';
            $data = (string) \gzcompress($data);
        } else {
            $entries = '';
        }
        $entries .= \sprintf('/Length %d', \strlen($data));
        $this->newObj();
        $this->put('<<' . $entries . '>>');
        $this->putStream($data);
        $this->endObj();
    }

    protected function putTrailer(): void
    {
        $this->putf('/Size %d', $this->objectNumber + 1);
        $this->putf('/Root %d 0 R', $this->objectNumber);
        $this->putf('/Info %d 0 R', $this->objectNumber - 1);
    }

    protected function putXObjectDictionary(): void
    {
        foreach ($this->images as $image) {
            $this->putf('/I%d %d 0 R', $image['i'], $image['n']);
        }
    }

    /**
     * @param resource $f
     *
     * @throws PdfException
     */
    protected function readInt($f): int
    {
        // Read a 4-byte integer from stream
        /** @var int[] $a */
        $a = (array) \unpack('Ni', $this->readStream($f, 4));

        return $a['i'];
    }

    /**
     * @phpstan-param  resource $f
     *
     * @throws PdfException
     */
    protected function readStream($f, int $n): string
    {
        // Read n bytes from stream
        $res = '';
        while ($n > 0 && !\feof($f)) {
            $str = \fread($f, $n);
            if (!\is_string($str)) {
                throw new PdfException('Error while reading stream.');
            }
            $n -= \strlen($str);
            $res .= $str;
        }
        if ($n > 0) {
            throw new PdfException('Unexpected end of stream.');
        }

        return $res;
    }

    protected function textString(string $s): string
    {
        // Format a text string
        if (!$this->isAscii($s)) {
            $s = $this->utf8toUtf16($s);
        }

        return '(' . $this->escape($s) . ')';
    }

    /**
     * @param array<int, int|int[]> $uv
     */
    protected function toUnicodeCmap(array $uv): string
    {
        $ranges = '';
        $nbr = 0;
        $chars = '';
        $nbc = 0;
        foreach ($uv as $c => $v) {
            if (\is_array($v)) {
                $ranges .= \sprintf("<%02X> <%02X> <%04X>\n", $c, $c + $v[1] - 1, $v[0]);
                ++$nbr;
            } else {
                $chars .= \sprintf("<%02X> <%04X>\n", $c, $v);
                ++$nbc;
            }
        }
        $s = "/CIDInit /ProcSet findresource begin\n";
        $s .= "12 dict begin\n";
        $s .= "begincmap\n";
        $s .= "/CIDSystemInfo\n";
        $s .= "<</Registry (Adobe)\n";
        $s .= "/Ordering (UCS)\n";
        $s .= "/Supplement 0\n";
        $s .= ">> def\n";
        $s .= "/CMapName /Adobe-Identity-UCS def\n";
        $s .= "/CMapType 2 def\n";
        $s .= "1 begincodespacerange\n";
        $s .= "<00> <FF>\n";
        $s .= "endcodespacerange\n";
        if ($nbr > 0) {
            $s .= "$nbr beginbfrange\n";
            $s .= $ranges;
            $s .= "endbfrange\n";
        }
        if ($nbc > 0) {
            $s .= "$nbc beginbfchar\n";
            $s .= $chars;
            $s .= "endbfchar\n";
        }
        $s .= "endcmap\n";
        $s .= "CMapName currentdict /CMap defineresource pop\n";
        $s .= "end\n";

        return $s . 'end';
    }

    protected function utf8encode(string $string): string
    {
        return (string) \iconv('ISO-8859-1', 'UTF-8', $string);
    }

    protected function utf8toUtf16(string $string): string
    {
        // Convert UTF-8 to UTF-16BE with BOM
        $res = "\xFE\xFF";

        return $res . (string) \iconv('UTF-8', 'UTF-16BE', $string);
    }
}
