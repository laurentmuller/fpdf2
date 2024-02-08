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
 * Represent a PDF document.
 *
 * @phpstan-type PdfPageSizeType = array{0: float, 1: float}
 * @phpstan-type ColorType = int<0, 255>
 * @phpstan-type UvType = array<int, int|int[]>
 * @phpstan-type PdfFontType = array{
 *     i: int,
 *     n: int,
 *     type: string,
 *     name: string,
 *     path: string,
 *     file: string,
 *     enc?: string,
 *     subsetted: bool,
 *     up: int,
 *     ut: int,
 *     uv?: UvType,
 *     desc: array<string, string>,
 *     cw: array<string, int>,
 *     diff?: string,
 *     originalsize: int,
 *     size1: int,
 *     size2: int,
 *     length1: int,
 *     length2?: int}
 * @phpstan-type PdfFontFileType = array{
 *     n: int,
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

    // The array of core font names.
    private const CORE_FONTS = [
        'courier',
        'helvetica',
        'times',
        'symbol',
        'zapfdingbats',
    ];

    // the empty color
    private const EMPTY_COLOR = '0 G';

    // the document is closed
    private const STATE_CLOSED = 3;
    // the end page has been called
    private const STATE_END_PAGE = 1;
    // the document has no page (not started)
    private const STATE_NO_PAGE = 0;
    // the start page has been called
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
    protected string $drawColor = self::EMPTY_COLOR;
    /**
     * The array of encodings.
     *
     * @phpstan-var array<string, int>
     */
    protected array $encodings = [];
    /**
     * The commands for filling color.
     */
    protected string $fillColor = self::EMPTY_COLOR;
    /**
     * The current font family.
     */
    protected string $fontFamily = '';
    /**
     * The array of font files.
     *
     * @phpstan-var array<string, PdfFontFileType>
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
    protected float $fontSizeInPoint = 9.0;
    /**
     * The current font style.
     */
    protected PdfFontStyle $fontStyle = PdfFontStyle::REGULAR;
    /**
     * The current page height in user unit.
     */
    protected float $height;
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
    protected array $metadata = [];
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
    protected string $pdfVersion = '1.3';
    /**
     * The right margin.
     */
    protected float $rightMargin;
    /**
     * The scale factor (number of points in user unit).
     */
    protected float $scaleFactor;
    /**
     * The current document state.
     */
    protected int $state = self::STATE_NO_PAGE;
    /**
     * The commands for text color.
     */
    protected string $textColor = self::EMPTY_COLOR;
    /**
     * The document title.
     */
    protected string $title = '';
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
    protected float $width;
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
     * Create a new instance.
     *
     * It allows to set up the page size, the page orientation and the unit of measure used in all methods (except for
     * font sizes).
     *
     * @param PdfOrientation  $orientation the page orientation
     * @param PdfUnit         $unit        the document unit to use
     * @param PdfSize|float[] $size        the page size
     *
     * @phpstan-param PdfSize|PdfPageSizeType $size
     */
    public function __construct(
        PdfOrientation $orientation = PdfOrientation::PORTRAIT,
        PdfUnit $unit = PdfUnit::MILLIMETER,
        PdfSize|array $size = PdfSize::A4
    ) {
        // Font path
        $this->fontPath = \defined('FPDF_FONTPATH') ? (string) FPDF_FONTPATH : __DIR__ . '/font/';
        // Scale factor
        $this->scaleFactor = $unit->getScaleFactor();
        // Page orientation
        $this->defaultOrientation = $orientation;
        $this->currentOrientation = $orientation;
        // Page size
        $size = $this->getPageSize($size);
        $this->defaultPageSize = $size;
        $this->currentPageSize = $size;
        if (PdfOrientation::PORTRAIT === $orientation) {
            $this->width = $size[0];
            $this->height = $size[1];
        } else {
            $this->width = $size[1];
            $this->height = $size[0];
        }
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
        // producer
        $this->setProducer('FPDF ' . self::VERSION);
        // font size and style
        $this->fontSize = $this->fontSizeInPoint / $this->scaleFactor;
    }

    /**
     * Imports a TrueType, OpenType or Type1 font and makes it available.
     *
     * It is necessary to generate a font definition file first with the <code>MakeFont</code> utility.
     *
     * The definition file (and the font file itself in case of embedding) must be present in:
     * <ul>
     * <li>The directory indicated by the 4th parameter (if that parameter is set).</li>
     * <li>The directory indicated by the <code>FPDF_FONTPATH</code> constant (if that constant is defined).</li>
     * <li>The font directory located in the same directory as this class.</li>
     * </ul>
     *
     * @param PdfFontName|string $family The font family. The name can be chosen arbitrarily. If it is a standard
     *                                   family name, it will override the corresponding font.
     * @param PdfFontStyle       $style  The font style. The default value is regular.
     * @param string             $file   The name of the font definition file. By default, it is built from the family
     *                                   and style, in lower case with no space.
     * @param string             $dir    The directory where to load the definition file. If not specified, the default
     *                                   directory will be used.
     *
     * @throws PdfException if the font file is not found
     *
     * @see PdfDocument::setFont()
     * @see PdfDocument::setFontSize()
     */
    public function addFont(
        PdfFontName|string $family,
        PdfFontStyle $style = PdfFontStyle::REGULAR,
        string $file = '',
        string $dir = ''
    ): self {
        if ($family instanceof PdfFontName) {
            $family = $family->value;
        }
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
            $dir .= \DIRECTORY_SEPARATOR;
        }
        /** @phpstan-var PdfFontType $info */
        $info = $this->loadFont($dir . $file);
        $info['i'] = \count($this->fonts) + 1;
        if (!empty($info['file'])) {
            // Embedded font
            $key = $dir . $info['file'];
            $info['file'] = $key;
            if ('TrueType' === $info['type']) {
                $this->fontFiles[$key] = [
                    'n' => 0,
                    'length1' => $info['originalsize'],
                ];
            } else {
                $this->fontFiles[$key] = [
                    'n' => 0,
                    'length1' => $info['size1'],
                    'length2' => $info['size2'],
                ];
            }
        }
        $this->fonts[$fontKey] = $info;

        return $this;
    }

    /**
     * Creates a new internal link and returns its identifier.
     *
     * An internal link is a clickable area, which directs to another place within the document. The identifier can
     * then be passed to <code>cell()</code>, <code>write()</code>, <code>image()</code> or <code>link()</code>. The
     * destination is defined with SetLink().
     *
     * @return int the link identifier
     */
    public function addLink(): int
    {
        $index = \count($this->links) + 1;
        $this->links[$index] = [0, 0];

        return $index;
    }

    /**
     * Adds a new page to the document.
     *
     * If a page is already present, the <code>footer()</code> method is called first to output the footer. Then the
     * page is added, the current position set to the top-left corner according to the left and top margins, and
     * <code>header()</code> is called to display the header. The font, which was set before calling is automatically
     * restored. There is no need to call <code>setFont()</code> again if you want to continue with the same font. The
     * same is true for colors and line width.
     *
     * The origin of the coordinate system is in the top-left corner and increasing ordinates go downwards.
     *
     * @param PdfOrientation|null  $orientation the page orientation or <code>null</code> to use the current orientation
     * @param PdfSize|float[]|null $size        the page size or <code>null</code> to use the current size
     * @param int|null             $rotation    the angle by which to rotate the page or <code>null</code> to use the
     *                                          current orientation. It must be a multiple of 90; positive values mean
     *                                          clockwise rotation.
     *
     * @phpstan-param PdfSize|PdfPageSizeType|null $size
     *
     * @throws PdfException if the document is closed
     */
    public function addPage(
        PdfOrientation|null $orientation = null,
        PdfSize|array|null $size = null,
        int|null $rotation = null
    ): self {
        if (self::STATE_CLOSED === $this->state) {
            throw new PdfException('The document is closed.');
        }
        // save context
        $fontFamily = $this->fontFamily;
        $fontStyle = $this->fontStyle;
        $fontSizeInPoint = $this->fontSizeInPoint;
        $lineWidth = $this->lineWidth;
        $drawColor = $this->drawColor;
        $fillColor = $this->fillColor;
        $textColor = $this->textColor;
        $colorFlag = $this->colorFlag;

        // Page footer and close page
        if ($this->page > 0) {
            $this->inFooter = true;
            $this->footer();
            $this->inFooter = false;
            $this->endPage();
        }
        // Start new page
        $this->beginPage($orientation, $size, $rotation);
        // Set line cap style to square
        $this->out('2 J');

        // restore context
        $this->lineWidth = $lineWidth;
        $this->outf('%.2F w', $lineWidth * $this->scaleFactor);
        if ('' !== $fontFamily) {
            $this->setFont($fontFamily, $fontStyle, $fontSizeInPoint);
        }
        $this->drawColor = $drawColor;
        if (self::EMPTY_COLOR !== $drawColor) {
            $this->out($drawColor);
        }
        $this->fillColor = $fillColor;
        if (self::EMPTY_COLOR !== $fillColor) {
            $this->out($fillColor);
        }
        $this->textColor = $textColor;
        $this->colorFlag = $colorFlag;

        // Page header
        $this->inHeader = true;
        $this->header();
        $this->inHeader = false;

        // restore context
        if ($this->lineWidth !== $lineWidth) {
            $this->lineWidth = $lineWidth;
            $this->outf('%.2F w', $lineWidth * $this->scaleFactor);
        }
        if ('' !== $fontFamily) {
            $this->setFont($fontFamily, $fontStyle, $fontSizeInPoint);
        }
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
     * Prints a cell (rectangular area) with optional borders, background color and character string.
     *
     * @param float            $width  the cell width. If 0.0, the cell extends up to the right margin.
     * @param float            $height the cell height
     * @param string           $text   the cell text
     * @param string|bool      $border indicates if borders must be drawn around the cell. The value can be either:
     *                                 <ul>
     *                                 <li>A boolean:
     *                                 <ul>
     *                                 <li><code>false</code>: No border (default value).</li>
     *                                 <li><code>true</code>: Draw a frame.</li>
     *                                 </ul>
     *                                 </li>
     *                                 <li>A string containing some or all of the following characters (in any order):
     *                                 <ul>
     *                                 <li><code>'L'</code>: Left border.</li>
     *                                 <li><code>'T'</code>: Top border.</li>
     *                                 <li><code>'R'</code>: Right border.</li>
     *                                 <li><code>'B'</code>: Bottom border.</li>
     *                                 </ul>
     *                                 </li>
     *                                 </ul>
     * @param PdfMove          $move   indicates where the current position should go after the call
     * @param PdfTextAlignment $align  the text alignment
     * @param bool             $fill   indicates if the cell background must be painted (<code>true</code>) or
     *                                 transparent (<code>false</code>)
     * @param string|int       $link   a URL or an identifier returned by <code>addLink()</code>
     *
     * @throws PdfException
     *
     * @see PdfDocument::multiCell()
     */
    public function cell(
        float $width = 0.0,
        float $height = 0.0,
        string $text = '',
        string|bool $border = false,
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
            $width = $this->getRemainingWidth();
        }
        $output = '';
        if ($fill || true === $border) {
            if ($fill) {
                $op = (true === $border) ? 'B' : 'f';
            } else {
                $op = 'S';
            }
            $output .= \sprintf(
                '%.2F %.2F %.2F %.2F re %s ',
                $this->x * $scaleFactor,
                ($this->height - $this->y) * $scaleFactor,
                $width * $scaleFactor,
                -$height * $scaleFactor,
                $op
            );
        }
        if (\is_string($border) && '' !== $border) {
            $x = $this->x;
            $y = $this->y;
            $border = \strtoupper($border);
            if (\str_contains($border, 'L')) {
                $output .= \sprintf(
                    '%.2F %.2F m %.2F %.2F l S ',
                    $x * $scaleFactor,
                    ($this->height - $y) * $scaleFactor,
                    $x * $scaleFactor,
                    ($this->height - ($y + $height)) * $scaleFactor
                );
            }
            if (\str_contains($border, 'T')) {
                $output .= \sprintf(
                    '%.2F %.2F m %.2F %.2F l S ',
                    $x * $scaleFactor,
                    ($this->height - $y) * $scaleFactor,
                    ($x + $width) * $scaleFactor,
                    ($this->height - $y) * $scaleFactor
                );
            }
            if (\str_contains($border, 'R')) {
                $output .= \sprintf(
                    '%.2F %.2F m %.2F %.2F l S ',
                    ($x + $width) * $scaleFactor,
                    ($this->height - $y) * $scaleFactor,
                    ($x + $width) * $scaleFactor,
                    ($this->height - ($y + $height)) * $scaleFactor
                );
            }
            if (\str_contains($border, 'B')) {
                $output .= \sprintf(
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
            $dx = match ($align) {
                PdfTextAlignment::RIGHT => $width - $this->cellMargin - $this->getStringWidth($text),
                PdfTextAlignment::CENTER => ($width - $this->getStringWidth($text)) / 2.0,
                default => $this->cellMargin,
            };
            if ($this->colorFlag) {
                $output .= \sprintf('q %s ', $this->textColor);
            }
            $output .= \sprintf(
                'BT %.2F %.2F Td (%s) Tj ET',
                ($this->x + $dx) * $scaleFactor,
                ($this->height - ($this->y + 0.5 * $height + 0.3 * $this->fontSize)) * $scaleFactor,
                $this->escape($text)
            );
            if ($this->underline) {
                $output .= ' ' . $this->doUnderline($this->x + $dx, $this->y + 0.5 * $height + 0.3 * $this->fontSize, $text);
            }
            if ($this->colorFlag) {
                $output .= ' Q';
            }
            if ('' !== $link) {
                $this->link(
                    $this->x + $dx,
                    $this->y + 0.5 * $height - 0.5 * $this->fontSize,
                    $this->getStringWidth($text),
                    $this->fontSize,
                    $link
                );
            }
        }
        if ('' !== $output) {
            $this->out($output);
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
     * Terminates the PDF document.
     *
     * It is unnecessary to call this method explicitly because <code>output()</code> does it automatically. If the
     * document contains no page, <code>addPage()</code> is called to prevent from getting an invalid document.
     *
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
     * This is a combination of the <code>addLink()</code> and the <code>setLink()</code> functions.
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

    /**
     * This method is used to render the page footer.
     *
     * It is automatically called before <code>addPage()</code> and <code>close()</code> methods and should not be
     * called directly by the application.
     *
     * The implementation is empty, so you have to subclass it and override the method if you want a specific
     * processing.
     *
     * @see PdfDocument::header()
     */
    public function footer(): void
    {
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
     * @return float the height or 0.0 if no printed cell
     */
    public function getLastHeight(): float
    {
        return $this->lastHeight;
    }

    /**
     * Gets the number of lines to use for the given text and width.
     *
     * Computes the number of lines a <code>multiCell()</code> of the given width will take.
     *
     * @param ?string $text       the text to compute
     * @param float   $width      the desired width. If 0.0, the width extends up to the right margin.
     * @param ?float  $cellMargin the desired cell margin or <code>null</code> to use current value
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
        $charWidths = $this->currentFont['cw'];
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
            $currentWidth += (float) $charWidths[$ch];
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

    /**
     * Gets the current page number.
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Gets the current page height.
     */
    public function getPageHeight(): float
    {
        return $this->height;
    }

    /**
     * Gets the current page width.
     */
    public function getPageWidth(): float
    {
        return $this->width;
    }

    /**
     * Gets the printable width.
     *
     * @return float the page width minus the left and right margins
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

    /**
     * Gets the length of a string in user unit.
     */
    public function getStringWidth(string $str): float
    {
        if (null !== $this->currentFont) {
            $width = 0.0;
            $charWidths = $this->currentFont['cw'];
            for ($i = 0,$len = \strlen($str); $i < $len; ++$i) {
                $width += (float) $charWidths[$str[$i]];
            }

            return $width * $this->fontSize / 1000.0;
        }

        return 0.0;
    }

    /**
     * Gets the document title.
     */
    public function getTitle(): string
    {
        return $this->title;
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
     * @phpstan-return array{0: float, 1: float}
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

    /**
     * This method is used to render the page header.
     *
     * It is automatically called after <code>addPage()</code> method and should not be called directly by the
     * application.
     *
     * The implementation is empty, so you have to subclass it and override the method if you want a specific
     * processing.
     *
     * @see PdfDocument::footer()
     */
    public function header(): void
    {
    }

    /**
     * Draws a horizontal line with current draw color and optionally the given line width.
     *
     * @param float      $beforeSpace the verticale space before the line
     * @param float      $afterSpace  the verticale space after the line
     * @param float|null $lineWidth   the optional line width or <code>null</code> to use current line width
     *
     * @throws PdfException
     */
    public function horizontalLine(float $beforeSpace = 1.0, float $afterSpace = 1.0, ?float $lineWidth = null): self
    {
        $x = $this->x;
        $y = $this->y + $beforeSpace;
        $width = $this->getPrintableWidth();
        $oldLineWidth = $this->lineWidth;
        if (null !== $lineWidth) {
            $this->setLineWidth($lineWidth);
        }
        $this->line($x, $y, $x + $width, $y);
        $this->setXY($x, $y + $afterSpace);
        if (null !== $lineWidth) {
            $this->setLineWidth($oldLineWidth);
        }

        return $this;
    }

    /**
     * Puts an image.
     *
     * The size it will take on the page can be specified in different ways:
     * <ul>
     * <li>Explicit width and height (expressed in user unit or dpi).</li>
     * <li>One explicit dimension, the other being calculated automatically in order to keep the original
     * proportions.</li>
     * <li>No explicit dimension, in which case the image is put at 96 dpi.</li>
     * </ul>
     *
     * Supported formats are JPEG, PNG and GIF. The GD extension is required for GIF.
     *
     * For JPEG, the following variants are allowed:
     * <ul>
     * <li>Gray scales.</li>
     * <li>True colors (24 bits).</li>
     * <li>CMYK (32 bits).</li>
     * </ul>
     *
     * For PNG, are allowed:
     * <ul>
     * <li>Gray scales on at most 8 bits (256 levels).</li>
     * <li>Indexed colors.</li>
     * <li>True colors (24 bits).</li>
     * </ul>
     *
     * For GIF:
     * <ul>
     * <li>In case of an animated GIF, only the first frame is displayed.</li>
     * </ul>
     *
     * For all:
     * <ul>
     * <li>Transparency is supported.</li>
     * <li>The format can be specified explicitly or inferred from the file extension.</li>
     * <li>It is possible to put a link on the image.</li>
     * </ul>
     *
     * <b>Remark: </b>If an image is used several times, only one copy is embedded in the file.
     *
     * @param string     $file   the path or the URL of the image
     * @param ?float     $x      the abscissa of the upper-left corner. If equal to <code>null</code>, the current
     *                           abscissa is used.
     * @param ?float     $y      the ordinate of the upper-left corner. If equal to <code>null</code>, the current
     *                           ordinate is used; moreover, a page break is triggered first if necessary (in case
     *                           automatic page breaking is enabled) and, after the call, the current ordinate
     *                           is move to the bottom of the image.
     * @param float      $width  the width of the image in the page. There are three cases:
     *                           <ul>
     *                           <li>If the value is positive, it represents the width in user unit.</li>
     *                           <li>If the value is negative, the absolute value represents the horizontal resolution
     *                           in dpi.</li>
     *                           <li>If the value is not specified or equal to zero, it is automatically
     *                           calculated.</li>
     *                           </ul>
     * @param float      $height the height of the image in the page. There are three cases:
     *                           <ul>
     *                           <li>If the value is positive, it represents the width in user unit.</li>
     *                           <li>If the value is negative, the absolute value represents the horizontal resolution
     *                           in dpi.</li>
     *                           <li>If the value is not specified or equal to zero, it is automatically
     *                           calculated.</li>
     *                           </ul>
     * @param string     $type   the image format. If not specified, the type is inferred from the file extension.
     * @param string|int $link   the URL or an identifier returned by <code>addLink()</code>
     *
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
            if ($this->y + $height > $this->pageBreakTrigger
                && !$this->inHeader && !$this->inFooter && $this->isAutoPageBreak()) {
                // Automatic page break
                $oldX = $this->x;
                $this->addPage($this->currentOrientation, $this->currentPageSize, $this->currentRotation);
                $this->x = $oldX;
            }
            $y = $this->y;
            $this->y += $height;
        }

        $x ??= $this->x;
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

    /**
     * Returns whenever a page break condition is met, the method is called, and the break is issued or not depending
     * on the returned value.
     *
     * The default implementation returns a value according to the mode selected by <code>setAutoPageBreak()</code>.
     * This method is called automatically and should not be called directly by the application.
     */
    public function isAutoPageBreak(): bool
    {
        return $this->autoPageBreak;
    }

    /**
     * Returns if the given height would not cause an overflow (new page).
     *
     * @param float  $height the desired height
     * @param ?float $y      the ordinate position or <code>null</code> to use the current position
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
     * Draws a line between two points.
     *
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

    /**
     * Performs a line break.
     *
     * The current abscissa goes back to the left margin and the ordinate increases by the amount passed parameter.
     *
     * @param float|null $height The height of the break. By default, the value equals the height of the last printed
     *                           cell.
     */
    public function lineBreak(?float $height = null): self
    {
        $this->x = $this->leftMargin;
        $this->y += $height ?? $this->lastHeight;

        return $this;
    }

    /**
     * Puts a link on a rectangular area of the page.
     *
     * Text or image links are generally put via <code>cell()</code>, <code>write()</code> or <code>image()</code>, but
     * this method can be useful for instance to define a clickable area inside an image.
     */
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
     * If the delta value is equal to 0, no move is set.
     *
     * @param float $delta  the delta value of the ordinate to move to
     * @param bool  $resetX whether to reset the abscissa
     */
    public function moveY(float $delta, bool $resetX = true): self
    {
        if (0.0 !== $delta) {
            $this->setY($this->y + $delta, $resetX);
        }

        return $this;
    }

    /**
     * This method allows printing text with line breaks.
     *
     *  They can be automatic (as soon as the text reaches the right border of the cell) or explicit
     *  (via the \n character). As many cells as necessary are output, one below the other. Text can be aligned,
     *  centered or justified. The cell block can be framed and the background painted.
     *
     * @param float            $width  the cell width. If 0.0, the cell extends up to the right margin.
     * @param float            $height the cell height
     * @param string           $text   the cell text
     * @param string|bool      $border indicates if borders must be drawn around the cell. The value can be either:
     *                                 <ul>
     *                                 <li>A boolean:
     *                                 <ul>
     *                                 <li><code>false</code>: No border (default value).</li>
     *                                 <li><code>true</code>: Draw a frame.</li>
     *                                 </ul>
     *                                 </li>
     *                                 <li>A string containing some or all of the following characters (in any order):
     *                                 <ul>
     *                                 <li><code>'L'</code>: Left border.</li>
     *                                 <li><code>'T'</code>: Top border.</li>
     *                                 <li><code>'R'</code>: Right border.</li>
     *                                 <li><code>'B'</code>: Bottom border.</li>
     *                                 </ul>
     *                                 </li>
     *                                 </ul>
     * @param PdfTextAlignment $align  the text alignment
     * @param bool             $fill   indicates if the cell background must be painted (true) or transparent (false)
     *
     * @throws PdfException
     *
     * @see PdfDocument::cell()
     */
    public function multiCell(
        float $width,
        float $height,
        string $text,
        string|bool $border = false,
        PdfTextAlignment $align = PdfTextAlignment::JUSTIFIED,
        bool $fill = false
    ): self {
        if (null === $this->currentFont) {
            throw new PdfException('No font has been set.');
        }

        $charWidths = $this->currentFont['cw'];
        if (0.0 === $width) {
            $width = $this->getRemainingWidth();
        }
        $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;
        $text = \rtrim(\str_replace("\r", '', $text));
        $len = \strlen($text);
        $border1 = false;
        $border2 = '';
        if (true === $border) {
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

        $index = 0;
        $newLine = 1;
        $lastIndex = 0;
        $sepIndex = -1;
        $newSeparator = 0;
        $lineSpace = 0.0;
        $currentWidth = 0.0;
        while ($index < $len) {
            $ch = $text[$index];
            if ("\n" === $ch) {
                // Explicit line break
                if ($this->wordSpacing > 0) {
                    $this->wordSpacing = 0;
                    $this->out('0 Tw');
                }
                $this->cell(
                    $width,
                    $height,
                    \substr($text, $lastIndex, $index - $lastIndex),
                    $border1,
                    PdfMove::BELOW,
                    $align,
                    $fill
                );
                ++$index;
                $sepIndex = -1;
                $lastIndex = $index;
                $currentWidth = 0.0;
                $newSeparator = 0;
                ++$newLine;
                if (\is_string($border) && 2 === $newLine) {
                    $border1 = $border2;
                }
                continue;
            }
            if (' ' === $ch) {
                $sepIndex = $index;
                $lineSpace = $currentWidth;
                ++$newSeparator;
            }
            $currentWidth += (float) $charWidths[$ch];
            if ($currentWidth > $widthMax) {
                // Automatic line break
                if (-1 === $sepIndex) {
                    if ($index === $lastIndex) {
                        ++$index;
                    }
                    if ($this->wordSpacing > 0) {
                        $this->wordSpacing = 0;
                        $this->out('0 Tw');
                    }
                    $this->cell(
                        $width,
                        $height,
                        \substr($text, $lastIndex, $index - $lastIndex),
                        $border1,
                        PdfMove::BELOW,
                        $align,
                        $fill
                    );
                } else {
                    if (PdfTextAlignment::JUSTIFIED === $align) {
                        $this->wordSpacing = ($newSeparator > 1)
                            ? ($widthMax - $lineSpace) / 1000.0 * $this->fontSize / (float) ($newSeparator - 1)
                            : 0.0;
                        $this->outf('%.3F Tw', $this->wordSpacing * $this->scaleFactor);
                    }
                    $this->cell(
                        $width,
                        $height,
                        \substr($text, $lastIndex, $sepIndex - $lastIndex),
                        $border1,
                        PdfMove::BELOW,
                        $align,
                        $fill
                    );
                    $index = $sepIndex + 1;
                }
                $sepIndex = -1;
                $lastIndex = $index;
                $currentWidth = 0.0;
                $newSeparator = 0;
                ++$newLine;
                if (\is_string($border) && 2 === $newLine) {
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
        if (\is_string($border) && \str_contains($border, 'B') && \is_string($border1)) {
            $border1 .= 'B';
        }
        $this->cell(
            $width,
            $height,
            \substr($text, $lastIndex, $index - $lastIndex),
            $border1,
            PdfMove::BELOW,
            $align,
            $fill
        );
        $this->x = $this->leftMargin;

        return $this;
    }

    /**
     * Send the document to a given destination: browser, file or string.
     *
     * In case of a browser, the PDF viewer may be used or a download may be forced. The method first calls
     * <code>close()</code> if necessary to terminate the document.
     *
     * @param PdfDestination $destination The destination where to send the document
     * @param string         $name        The name of the file. It is ignored in case of
     *                                    destination <code>PdfDestination::STRING</code>. The default value
     *                                    is 'doc.pdf'.
     * @param bool           $isUTF8      Indicates if name is encoded in ISO-8859-1 (false) or UTF-8 (true).
     *                                    Only used for destinations <code>PdfDestination::INLINE</code> and
     *                                    <code>PdfDestination::DOWNLOAD</code>.
     *
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
     * Outputs a rectangle.
     *
     * It can be drawn (border only), filled (with no border) or both.
     *
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

    /**
     * Defines an alias for the total number of pages.
     *
     * It will be substituted as the document is closed.
     */
    public function setAliasNumberPages(string $aliasNumberPages = '{nb}'): self
    {
        $this->aliasNumberPages = $aliasNumberPages;

        return $this;
    }

    /**
     * Defines the author of the document.
     *
     * @param string $author the name of the author
     * @param bool   $isUTF8 indicates if the string is encoded in ISO-8859-1 (false) or UTF-8 (true)
     */
    public function setAuthor(string $author, bool $isUTF8 = false): self
    {
        return $this->addMetaData('Author', $author, $isUTF8);
    }

    /**
     * Enables or disables the automatic page breaking mode.
     *
     * When enabling, the second parameter is the distance from the bottom of the page that defines the triggering
     * limit.
     */
    public function setAutoPageBreak(bool $autoPageBreak, float $bottomMargin = 0): self
    {
        $this->autoPageBreak = $autoPageBreak;
        $this->bottomMargin = $bottomMargin;
        $this->pageBreakTrigger = $this->height - $bottomMargin;

        return $this;
    }

    /**
     * Sets the cell margin.
     *
     * The minimum value allowed is 0.
     */
    public function setCellMargin(float $margin): self
    {
        $this->cellMargin = \max(0.0, $margin);

        return $this;
    }

    /**
     * Activates or deactivates page compression.
     *
     * When activated, the internal representation of each page is compressed, which leads to a compression ratio of
     * about 2 for the resulting document. Compression is on by default.
     *
     * <b>Note:</b> the Zlib extension is required for this feature. If not present, compression will be turned off.
     */
    public function setCompression(bool $compression): self
    {
        $this->compression = \function_exists('gzcompress') ? $compression : false;

        return $this;
    }

    /**
     * Defines the creator of the document.
     *
     * This is typically the name of the application that generates the PDF.
     *
     * @param string $creator the name of the creator
     * @param bool   $isUTF8  indicates if the string is encoded in ISO-8859-1 (false) or UTF-8 (true)
     */
    public function setCreator(string $creator, bool $isUTF8 = false): self
    {
        return $this->addMetaData('Creator', $creator, $isUTF8);
    }

    /**
     * Defines the way the document is to be displayed by the viewer.
     *
     * The zoom level can be set: pages can be displayed entirely on screen, occupy the full width of the window,
     * use real size, be scaled by a specific zooming factor or use viewer default (configured in the Preferences menu
     * of Adobe Reader). The page layout can be specified too: single at once, continuous display, two columns or
     * viewer default.
     *
     * @param PdfZoom|int $zoom   The zoom to use. It can be one of the following values:
     *                            <ul>
     *                            <li>A PDF zoom enumeration.</li>
     *                            <li>A number indicating the zooming factor to use.</li>
     *                            </ul>
     * @param PdfLayout   $layout the page layout
     */
    public function setDisplayMode(
        PdfZoom|int $zoom = PdfZoom::DEFAULT,
        PdfLayout $layout = PdfLayout::DEFAULT
    ): self {
        $this->zoom = $zoom;
        $this->layout = $layout;

        return $this;
    }

    /**
     * Defines the color used for all drawing operations (lines, rectangles, and cell borders).
     *
     * It can be expressed in RGB components or gray scale. The method can be called before the first page is created
     * and the value is retained from page to page.
     *
     * @param int  $r If <code>$g</code> and <code>$b</code> are given, red component; if not, indicates the gray
     *                level. Value between 0 and 255.
     * @param ?int $g the green component (between 0 and 255)
     * @param ?int $b the blue component (between 0 and 255)
     *
     * @throws PdfException
     *
     * @see PdfDocument::setFillColor()
     * @see PdfDocument::setTextColor()
     *
     * @phpstan-param ColorType $r
     * @phpstan-param ColorType|null $g
     * @phpstan-param ColorType|null $b
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
     * Defines the color used for all filling operations (filled rectangles and cell backgrounds).
     *
     * It can be expressed in RGB components or gray scale. The method can be called before the first page is created
     * and the value is retained from page to page.
     *
     * @param int  $r If <code>$g</code> and <code>$b</code> are given, red component; if not, indicates the gray
     *                level. Value between 0 and 255.
     * @param ?int $g the green component (between 0 and 255)
     * @param ?int $b the blue component (between 0 and 255)
     *
     * @throws PdfException
     *
     * @see PdfDocument::setDrawColor()
     * @see PdfDocument::setTextColor()
     *
     * @phpstan-param ColorType $r
     * @phpstan-param ColorType|null $g
     * @phpstan-param ColorType|null $b
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
     * Sets the font used to print character strings.
     *
     * It is mandatory to call this method at least once before printing text.
     *
     * The font can be either a standard one or a font added by the <code>addFont()</code> method. Standard fonts use
     * the Windows encoding cp1252 (Western Europe).
     *
     * The method can be called before the first page is created and the font is kept from page to page.
     *
     * If you just wish to change the current font size, it is simpler to call <code>setFontSize()</code>.
     *
     * @param PdfFontName|string $family The font family. It can be either a font name enumeration, a name defined by
     *                                   <code>addFont()</code> or one of the standard families (case-insensitive):
     *                                   <ul>
     *                                   <li><code>'Courier'</code>: Fixed-width.</li>
     *                                   <li><code>'Helvetica'</code> or <code>Arial</code>: Synonymous: sans
     *                                   serif.</li>
     *                                   <li><code>'Symbol'</code>: Symbolic.</li>
     *                                   <li><code>'ZapfDingbats'</code>: Symbolic.</li>
     *                                   </ul>
     *                                   It is also possible to pass an empty string. In that case, the current family
     *                                   is kept.
     * @param PdfFontStyle       $style  The font style. The default value is regular.
     * @param float              $size   The font size in points or 0.0 to use the current size. If no size has been
     *                                   specified since the beginning of the document, the value is 9.0.
     *
     * @throws PdfException
     *
     * @see PdfDocument::addFont()
     */
    public function setFont(PdfFontName|string $family = '', PdfFontStyle $style = PdfFontStyle::REGULAR, float $size = 0): self
    {
        if ($family instanceof PdfFontName) {
            $family = $family->value;
        }
        $family = '' === $family ? $this->fontFamily : \strtolower($family);
        $this->underline = $style->isUnderLine();
        if (0.0 === $size) {
            $size = $this->fontSizeInPoint;
        }
        // Test if font is already selected
        if ($this->fontFamily === $family && $this->fontStyle === $style && $this->fontSizeInPoint === $size) {
            return $this;
        }
        // Remove underline style
        if ($this->underline) {
            $style = $style->removeUnderLine();
        }
        // Test if font is already loaded
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
     * Defines the size (in points) of the current font.
     *
     * @throws PdfException
     *
     * @see PdfDocument::setFont()
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

    /**
     * Associates keywords with the document.
     *
     * @param string $keywords The list of keywords separated by space
     * @param bool   $isUTF8   Indicates if the string is encoded in ISO-8859-1 (false) or UTF-8 (true)
     */
    public function setKeywords(string $keywords, bool $isUTF8 = false): self
    {
        return $this->addMetaData('Keywords', $keywords, $isUTF8);
    }

    /**
     * Defines the left margin.
     *
     * The method can be called before creating the first page.
     */
    public function setLeftMargin(float $leftMargin): self
    {
        $this->leftMargin = $leftMargin;
        if ($this->page > 0 && $this->x < $leftMargin) {
            $this->x = $leftMargin;
        }

        return $this;
    }

    /**
     * Defines the line width.
     *
     * By default, the value equals 0.2 mm. The method can be called before the first page is created and the value is
     * retained from page to page.
     *
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

    /**
     * Defines the page and position a link points to.
     *
     * @param int       $link the link identifier returned by <code>addLink()</code>
     * @param int|float $y    Ordinate of target position; -1 indicates the current position. The default value is 0
     *                        (top of page).
     * @param int       $page Number of target page; -1 indicates the current page. This is the default value.
     */
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

    /**
     * Defines the left, top and right margins.
     *
     * By default, they equal 1 cm. Call this method to change them.
     */
    public function setMargins(float $leftMargin, float $topMargin, ?float $rightMargin = null): self
    {
        $this->leftMargin = $leftMargin;
        $this->topMargin = $topMargin;
        $this->rightMargin = $rightMargin ?? $leftMargin;

        return $this;
    }

    /**
     * Defines the producer of the document.
     *
     * @param string $producer the producer
     * @param bool   $isUTF8   Indicates if the string is encoded in ISO-8859-1 (false) or UTF-8 (true)
     */
    public function setProducer(string $producer, bool $isUTF8 = false): self
    {
        return $this->addMetaData('Producer', $producer, $isUTF8);
    }

    /**
     * Defines the right margin.
     *
     * The method can be called before creating the first page.
     */
    public function setRightMargin(float $rightMargin): self
    {
        $this->rightMargin = $rightMargin;

        return $this;
    }

    /**
     * Defines the subject of the document.
     *
     * @param string $subject the subject
     * @param bool   $isUTF8  Indicates if the string is encoded in ISO-8859-1 (false) or UTF-8 (true)
     */
    public function setSubject(string $subject, bool $isUTF8 = false): self
    {
        return $this->addMetaData('Subject', $subject, $isUTF8);
    }

    /**
     * Defines the color used for text.
     *
     * It can be expressed in RGB components or gray scale. The method can be called before the first page is created
     * and the value is retained from page to page.
     *
     * @param int  $r If <code>$g</code> and <code>$b</code> are given, red component; if not, indicates the gray
     *                level. Value between 0 and 255.
     * @param ?int $g the green component (between 0 and 255)
     * @param ?int $b the blue component (between 0 and 255)
     *
     * @see PdfDocument::setDrawColor()
     * @see PdfDocument::setFillColor()
     *
     * @phpstan-param ColorType $r
     * @phpstan-param ColorType|null $g
     * @phpstan-param ColorType|null $b
     */
    public function setTextColor(int $r, ?int $g = null, ?int $b = null): self
    {
        $this->textColor = $this->parseColor($r, $g, $b);
        $this->colorFlag = ($this->fillColor !== $this->textColor);

        return $this;
    }

    /**
     * Defines the title of the document.
     *
     * @param string $title  the title
     * @param bool   $isUTF8 Indicates if the string is encoded in ISO-8859-1 (false) or UTF-8 (true)
     */
    public function setTitle(string $title, bool $isUTF8 = false): self
    {
        $this->title = $title;

        return $this->addMetaData('Title', $title, $isUTF8);
    }

    /**
     * Defines the top margin.
     *
     * The method can be called before creating the first page.
     */
    public function setTopMargin(float $topMargin): self
    {
        $this->topMargin = $topMargin;

        return $this;
    }

    /**
     * Defines the abscissa of the current position.
     *
     * If the passed value is negative, it is relative to the right of the page.
     */
    public function setX(float $x): self
    {
        $this->x = $x >= 0 ? $x : $this->width + $x;

        return $this;
    }

    /**
     * Defines the abscissa and ordinate of the current position.
     *
     * If the passed values are negative, they are relative respectively to the right and bottom of the page.
     */
    public function setXY(float $x, float $y): self
    {
        $this->SetX($x);
        $this->SetY($y, false);

        return $this;
    }

    /**
     * Sets the ordinate and optionally moves the current abscissa back to the left margin.
     *
     * If the value is negative, it is relative to the bottom of the page.
     */
    public function setY(float $y, bool $resetX = true): self
    {
        $this->y = $y >= 0 ? $y : $this->height + $y;
        if ($resetX) {
            $this->x = $this->leftMargin;
        }

        return $this;
    }

    /**
     * Prints a character string.
     *
     * The origin is on the left of the first character, on the baseline. This method allows to place a string
     * precisely on the page, but it is usually easier to use <code>cell()</code>, <code>multiCell()</code> or
     * <code>write()</code> which are the standard methods to print text.
     *
     * @param float  $x    the abscissa of the origin
     * @param float  $y    the ordinate of the origin
     * @param string $text the string to print
     *
     * @throws PdfException
     */
    public function text(float $x, float $y, string $text): self
    {
        if (null === $this->currentFont) {
            throw new PdfException('No font has been set.');
        }
        $output = \sprintf(
            'BT %.2F %.2F Td (%s) Tj ET',
            $x * $this->scaleFactor,
            ($this->height - $y) * $this->scaleFactor,
            $this->escape($text)
        );
        if ($this->underline && '' !== $text) {
            $output .= ' ' . $this->doUnderline($x, $y, $text);
        }
        if ($this->colorFlag) {
            $output = \sprintf('q %s %s Q', $this->textColor, $output);
        }
        $this->out($output);

        return $this;
    }

    /**
     * Ensure that this version is equal to or greater than the given version.
     *
     * @param string $version the minimum version to set
     */
    public function updateVersion(string $version): self
    {
        if (\version_compare($this->pdfVersion, $version, '<')) {
            $this->pdfVersion = $version;
        }

        return $this;
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
     * This method prints text from the current position.
     *
     * When the right margin is reached (or the \n character is met) a line break occurs and text continues from the
     * left margin. Upon method exit, the current position is left just at the end of the text. It is possible to put
     * a link on the text.
     *
     * @param float      $height the line height
     * @param string     $text   the string to print
     * @param string|int $link   a URL or an identifier returned by <code>addLink()</code>
     *
     * @throws PdfException
     */
    public function write(float $height, string $text, string|int $link = ''): self
    {
        if (null === $this->currentFont) {
            throw new PdfException('No font has been set.');
        }
        $width = $this->getRemainingWidth();
        $charWidths = $this->currentFont['cw'];
        $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;
        $text = \str_replace("\r", '', $text);
        $len = \strlen($text);
        $sep = -1;
        $index = 0;
        $currentIndex = 0;
        $currentWidth = 0.0;
        $newLine = 1;
        while ($index < $len) {
            // Get next character
            $c = $text[$index];
            if ("\n" === $c) {
                // Explicit line break
                $this->cell(
                    $width,
                    $height,
                    \substr($text, $currentIndex, $index - $currentIndex),
                    false,
                    PdfMove::BELOW,
                    PdfTextAlignment::LEFT,
                    false,
                    $link
                );
                ++$index;
                $sep = -1;
                $currentIndex = $index;
                $currentWidth = 0.0;
                if (1 === $newLine) {
                    $this->x = $this->leftMargin;
                    $width = $this->getRemainingWidth();
                    $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;
                }
                ++$newLine;
                continue;
            }
            if (' ' === $c) {
                $sep = $index;
            }
            $currentWidth += (float) $charWidths[$c];
            if ($currentWidth > $widthMax) {
                // Automatic line break
                if (-1 === $sep) {
                    if ($this->x > $this->leftMargin) {
                        // Move to next line
                        $this->x = $this->leftMargin;
                        $this->y += $height;
                        $width = $this->getRemainingWidth();
                        $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;
                        ++$index;
                        ++$newLine;
                        continue;
                    }
                    if ($index === $currentIndex) {
                        ++$index;
                    }
                    $this->cell(
                        $width,
                        $height,
                        \substr($text, $currentIndex, $index - $currentIndex),
                        false,
                        PdfMove::BELOW,
                        PdfTextAlignment::LEFT,
                        false,
                        $link
                    );
                } else {
                    $this->cell(
                        $width,
                        $height,
                        \substr($text, $currentIndex, $sep - $currentIndex),
                        false,
                        PdfMove::BELOW,
                        PdfTextAlignment::LEFT,
                        false,
                        $link
                    );
                    $index = $sep + 1;
                }
                $sep = -1;
                $currentIndex = $index;
                $currentWidth = 0.0;
                if (1 === $newLine) {
                    $this->x = $this->leftMargin;
                    $width = $this->getRemainingWidth();
                    $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;
                }
                ++$newLine;
            } else {
                ++$index;
            }
        }
        // Last chunk
        if ($index !== $currentIndex) {
            $this->cell(
                $currentWidth / 1000.0 * $this->fontSize,
                $height,
                \substr($text, $currentIndex),
                false,
                PdfMove::RIGHT,
                PdfTextAlignment::LEFT,
                false,
                $link
            );
        }

        return $this;
    }

    /**
     * Add the given key/value pair to document meta-data.
     *
     * @phpstan-param non-empty-string $key
     */
    protected function addMetaData(string $key, string $value, bool $isUTF8 = false): self
    {
        $this->metadata[$key] = $isUTF8 ? $value : $this->convertIsoToUtf8($value);

        return $this;
    }

    /**
     * Begin a new page.
     *
     * @phpstan-param PdfSize|PdfPageSizeType|null $size
     *
     * @throws PdfException
     */
    protected function beginPage(
        ?PdfOrientation $orientation = null,
        PdfSize|array|null $size = null,
        int|null $rotation = null
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
        $rotation ??= $this->currentRotation;
        if (0 !== $rotation) {
            if (0 !== $rotation % 90) {
                throw new PdfException(\sprintf('Incorrect rotation value: %d.', $rotation));
            }
            $this->pageInfos[$this->page]['rotation'] = $rotation;
        }
        $this->currentRotation = $rotation;
    }

    /**
     * Check if the output is not yet started.
     *
     * @throws PdfException if some data has already been output
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
     * Convert the given string from ISO-8859-1 to UTF-8.
     */
    protected function convertIsoToUtf8(string $str): string
    {
        return (string) \iconv('ISO-8859-1', 'UTF-8', $str);
    }

    /**
     * Convert the given string from UTF8 to UTF-16BE.
     */
    protected function convertUtf8ToUtf16(string $str): string
    {
        return "\xFE\xFF" . (string) \iconv('UTF-8', 'UTF-16BE', $str);
    }

    /**
     * Output the underline font.
     *
     * @throws PdfException
     */
    protected function doUnderline(float $x, float $y, string $str): string
    {
        if (null === $this->currentFont) {
            throw new PdfException('No font has been set.');
        }

        // Underline text
        $up = $this->currentFont['up'];
        $ut = $this->currentFont['ut'];
        $width = $this->getStringWidth($str) + $this->wordSpacing * (float) \substr_count($str, ' ');

        return \sprintf(
            '%.2F %.2F %.2F %.2F re f',
            $x * $this->scaleFactor,
            ($this->height - ($y - (float) $up / 1000.0 * $this->fontSize)) * $this->scaleFactor,
            $width * $this->scaleFactor,
            (float) -$ut / 1000.0 * $this->fontSizeInPoint
        );
    }

    /**
     * Output the end of document.
     *
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

    /**
     * Output the end object.
     */
    protected function endObj(): void
    {
        $this->put('endobj');
    }

    /**
     * Output the end of page.
     */
    protected function endPage(): void
    {
        $this->state = self::STATE_END_PAGE;
    }

    /**
     * Escape the given string.
     */
    protected function escape(string $str): string
    {
        // Escape special characters
        if (\str_contains($str, '(') || \str_contains($str, ')')
            || \str_contains($str, '\\') || \str_contains($str, "\r")) {
            return \str_replace(['\\', '(', ')', "\r"], ['\\\\', '\\(', '\\)', '\\r'], $str);
        }

        return $str;
    }

    /**
     * Gets the buffer offset.
     */
    protected function getOffset(): int
    {
        return \strlen($this->buffer);
    }

    /**
     * Gets the page size.
     *
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

    /**
     * Encode the given name/value pair parameter.
     */
    protected function httpEncode(string $name, string $value, bool $isUTF8): string
    {
        // Encode HTTP header field parameter
        if ($this->isAscii($value)) {
            return \sprintf('%s="%s"', $name, $value);
        }
        if (!$isUTF8) {
            $value = $this->convertIsoToUtf8($value);
        }

        return \sprintf("%s*=UTF-8''%s", $name, \rawurlencode($value));
    }

    /**
     * Returns if the given string is only containing ASCII characters.
     */
    protected function isAscii(string $str): bool
    {
        return \mb_check_encoding($str, 'ASCII');
    }

    /**
     * Load the font from the given file path.
     *
     * @throws PdfException
     *
     * @psalm-suppress UnresolvableInclude
     * @psalm-suppress UndefinedVariable
     * @psalm-suppress UnusedVariable
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

    /**
     * Output a new object.
     */
    protected function newObj(?int $index = null): void
    {
        // Begin a new object
        if (null === $index) {
            $index = ++$this->objectNumber;
        }
        $this->offsets[$index] = $this->getOffset();
        $this->putf('%d 0 obj', $index);
    }

    /**
     * Output the given string.
     *
     * @throws PdfException
     */
    protected function out(string $output): void
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
                $this->pages[$this->page] .= $output . "\n";
                break;
        }
    }

    /**
     * Output a formatted string.
     *
     * @throws PdfException
     */
    protected function outf(string $format, string|int|float ...$values): void
    {
        $this->out(\sprintf($format, ...$values));
    }

    /**
     * Parse the given RGB color.
     */
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
     * Parse the given GIF image.
     *
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
        $stream = \fopen('php://temp', 'rb+');
        if (!\is_resource($stream)) {
            throw new PdfException('Unable to create memory stream.');
        }

        try {
            \fwrite($stream, $data);
            \rewind($stream);

            return $this->parsePngStream($stream, $file);
        } finally {
            \fclose($stream);
        }
    }

    /**
     * Parse the given JPG image.
     *
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

        return [
            'w' => $size[0],
            'h' => $size[1],
            'cs' => $color_space,
            'bpc' => $bpc,
            'f' => 'DCTDecode',
            'data' => $data,
        ];
    }

    /**
     * Parse the given PNG image.
     *
     * @throws PdfException
     */
    protected function parsePng(string $file): array
    {
        // Extract info from a PNG file
        $stream = \fopen($file, 'r');
        if (!\is_resource($stream)) {
            throw new PdfException(\sprintf('Can not open image file: %s.', $file));
        }

        try {
            return $this->parsePngStream($stream, $file);
        } finally {
            \fclose($stream);
        }
    }

    /**
     * Parse the given PNG stream.
     *
     * @param resource $stream
     *
     * @throws PdfException
     */
    protected function parsePngStream($stream, string $file): array
    {
        // Check signature
        // [0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A];
        $header = \chr(137) . 'PNG' . \chr(13) . \chr(10) . \chr(26) . \chr(10);
        if ($this->readStream($stream, 8) !== $header) {
            throw new PdfException(\sprintf('File is not a PNG image: %s.', $file));
        }
        // Read header chunk
        $this->readStream($stream, 4);
        if ('IHDR' !== $this->readStream($stream, 4)) {
            throw new PdfException(\sprintf('Incorrect PNG file: %s.', $file));
        }
        $width = $this->readInt($stream);
        $height = $this->readInt($stream);
        $bitsPerComponent = \ord($this->readStream($stream, 1));
        if ($bitsPerComponent > 8) {
            throw new PdfException(\sprintf('16-bit depth not supported: %s.', $file));
        }
        $ct = \ord($this->readStream($stream, 1));
        $colorSpace = match ($ct) {
            0, 4 => 'DeviceGray',
            2, 6 => 'DeviceRGB',
            3 => 'Indexed',
            default => throw new PdfException(\sprintf('Unknown color type: %s.', $file)),
        };
        if (0 !== \ord($this->readStream($stream, 1))) {
            throw new PdfException(\sprintf('Unknown compression method: %s.', $file));
        }
        // @phpstan-ignore-next-line
        if (0 !== \ord($this->readStream($stream, 1))) {
            throw new PdfException(\sprintf('Unknown filter method: %s.', $file));
        }
        // @phpstan-ignore-next-line
        if (0 !== \ord($this->readStream($stream, 1))) {
            throw new PdfException(\sprintf('Interlacing not supported: %s.', $file));
        }
        $this->readStream($stream, 4);
        $dp = \sprintf(
            '/Predictor 15 /Colors %d /BitsPerComponent %d /Columns %d',
            'DeviceRGB' === $colorSpace ? 3 : 1,
            $bitsPerComponent,
            $width
        );

        // Scan chunks looking for the palette, transparency and image data
        $data = '';
        $palette = '';
        $transparencies = '';
        do {
            $length = $this->readInt($stream);
            $type = $this->readStream($stream, 4);
            switch ($type) {
                case 'PLTE':
                    // Read palette
                    $palette = $this->readStream($stream, $length);
                    $this->readStream($stream, 4);
                    break;
                case 'tRNS':
                    // Read transparency info
                    $t = $this->readStream($stream, $length);
                    if (0 === $ct) {
                        $transparencies = [\ord(\substr($t, 1, 1))];
                    } elseif (2 === $ct) {
                        $transparencies = [\ord(\substr($t, 1, 1)), \ord(\substr($t, 3, 1)), \ord(\substr($t, 5, 1))];
                    } else {
                        $pos = \strpos($t, \chr(0));
                        if (false !== $pos) {
                            $transparencies = [$pos];
                        }
                    }
                    $this->readStream($stream, 4);
                    break;
                case 'IDAT':
                    // Read image data block
                    $data .= $this->readStream($stream, $length);
                    $this->readStream($stream, 4);
                    break;
                default:
                    $this->readStream($stream, $length + 4);
                    break;
            }
        } while ($length);

        if ('Indexed' === $colorSpace && '' === $palette) {
            throw new PdfException(\sprintf('Missing palette in %s.', $file));
        }
        $info = [
            'w' => $width,
            'h' => $height,
            'cs' => $colorSpace,
            'bpc' => $bitsPerComponent,
            'f' => 'FlateDecode',
            'dp' => $dp,
            'pal' => $palette,
            'trns' => $transparencies,
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
                $len = 2 * $width;
                for ($i = 0; $i < $height; ++$i) {
                    $pos = (1 + $len) * $i;
                    $color .= $data[$pos];
                    $alpha .= $data[$pos];
                    $line = \substr($data, $pos + 1, $len);
                    $color .= \preg_replace('/(.)./s', '$1', $line);
                    $alpha .= \preg_replace('/.(.)/s', '$1', $line);
                }
            } else {
                // RGB image
                $len = 4 * $width;
                for ($i = 0; $i < $height; ++$i) {
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
            $this->updateVersion('1.4');
            $this->withAlpha = true;
        }
        $info['data'] = $data;

        return $info;
    }

    /**
     * Put the given string to this buffer.
     */
    protected function put(string|int $value): void
    {
        $this->buffer .= \sprintf("%s\n", $value);
    }

    /**
     * Put the catalog to this buffer.
     */
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

    /**
     * Put a formatted string to this buffer.
     */
    protected function putf(string $format, string|int|float ...$values): void
    {
        $this->put(\sprintf($format, ...$values));
    }

    /**
     * Put fonts to this buffer.
     *
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
                $length1 = $info['length1'];
                $length2 = $info['length2'];
                $content = \substr($content, 6, $length1) . \substr($content, 6 + $length1 + 6, $length2);
            }
            $this->putf('<</Length %d', \strlen($content));
            if ($compressed) {
                $this->put('/Filter /FlateDecode');
            }
            $this->putf('/Length1 %d', $info['length1']);
            if (isset($info['length2'])) {
                $this->putf('/Length2 %d /Length3 0', $info['length2']);
            }
            $this->put('>>');
            $this->putStream($content);
            $this->endObj();
        }

        foreach ($this->fonts as $key => $font) {
            // Encoding
            if (isset($font['diff'])) {
                $enc = $font['enc'] ?? '';
                if (!isset($this->encodings[$enc])) {
                    $this->newObj();
                    $this->putf('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [%s]>>', $font['diff']);
                    $this->endObj();
                    $this->encodings[$enc] = $this->objectNumber;
                }
            }
            // ToUnicode CMap
            $mapKey = '';
            $name = $font['name'];
            if (isset($font['uv'])) {
                $mapKey = $font['enc'] ?? $name;
                if (!isset($this->cMaps[$mapKey])) {
                    $map = $this->toUnicodeCmap($font['uv']);
                    $this->putStreamObject($map);
                    $this->cMaps[$mapKey] = $this->objectNumber;
                }
            }
            // Font object
            $this->fonts[$key]['n'] = $this->objectNumber + 1;
            if ($font['subsetted']) {
                $name = 'AAAAAA+' . $name;
            }
            $type = $font['type'];
            if ('Core' === $type) {
                // Core font
                $this->newObj();
                $this->put('<</Type /Font');
                $this->putf('/BaseFont /%s', $name);
                $this->put('/Subtype /Type1');
                if ('Symbol' !== $name && 'ZapfDingbats' !== $name) {
                    $this->put('/Encoding /WinAnsiEncoding');
                }
                if (isset($font['uv'])) {
                    $this->putf('/ToUnicode %d 0 R', $this->cMaps[$mapKey]);
                }
                $this->put('>>');
                $this->endObj();
            } elseif ('Type1' === $type || 'TrueType' === $type) {
                // Additional Type1 or TrueType/OpenType font
                $this->newObj();
                $this->put('<</Type /Font');
                $this->putf('/BaseFont /%s', $name);
                $this->putf('/Subtype /%s', $type);
                $this->put('/FirstChar 32 /LastChar 255');
                $this->putf('/Widths %d 0 R', $this->objectNumber + 1);
                $this->putf('/FontDescriptor %d 0 R', $this->objectNumber + 2);
                if (isset($font['diff']) && isset($font['enc'])) {
                    $this->putf('/Encoding %d 0 R', $this->encodings[$font['enc']]);
                } else {
                    $this->put('/Encoding /WinAnsiEncoding');
                }
                if (isset($font['uv'])) {
                    $this->putf('/ToUnicode %d 0 R', $this->cMaps[$mapKey]);
                }
                $this->put('>>');
                $this->endObj();
                // Widths
                $this->newObj();
                $charWidths = $font['cw'];
                $output = '[';
                for ($i = 32; $i <= 255; ++$i) {
                    $output .= \sprintf('%d ', $charWidths[\chr($i)]);
                }
                $output .= ']';
                $this->put($output);
                $this->endObj();
                // Descriptor
                $this->newObj();
                $output = \sprintf('<</Type /FontDescriptor /FontName /%s', $name);
                foreach ($font['desc'] as $descKey => $descValue) {
                    $output .= \sprintf(' /%s %s', $descKey, $descValue);
                }
                if (!empty($font['file'])) {
                    $n = $this->fontFiles[$font['file']]['n'];
                    $output .= \sprintf(' /FontFile%s %d 0 R', 'Type1' === $type ? '' : '2', $n);
                }
                $output .= '>>';
                $this->put($output);
                $this->endObj();
            } else {
                // Allow for additional types
                $method = 'put' . \ucfirst(\strtolower($type));
                if (!\method_exists($this, $method)) {
                    throw new PdfException(\sprintf('Unsupported font type: %s.', $type));
                }
                $this->$method($font);
            }
        }
    }

    /**
     * Put header to this buffer.
     */
    protected function putHeader(): void
    {
        $this->putf('%%PDF-%s', $this->pdfVersion);
    }

    /**
     * Put image to this buffer.
     *
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
            $this->putf(
                '/ColorSpace [/Indexed /DeviceRGB %d %d 0 R]',
                \intdiv(\strlen($info['pal']), 3) - 1,
                $this->objectNumber + 1
            );
        } else {
            $this->putf('/ColorSpace /%s', $info['cs']);
            if ('DeviceCMYK' === $info['cs']) {
                $this->put('/Decode [1 0 1 0 1 0 1 0]');
            }
        }
        $this->putf('/BitsPerComponent %d', $info['bpc']);
        if (isset($info['f'])) {
            $this->putf('/Filter /%s', $info['f']);
        }
        if (isset($info['dp'])) {
            $this->putf('/DecodeParms <<%s>>', $info['dp']);
        }
        if (isset($info['trns']) && \is_array($info['trns'])) {
            $trns = '';
            for ($i = 0, $len = \count($info['trns']); $i < $len; ++$i) {
                $trns .= \sprintf('%1$s %1$s ', $info['trns'][$i]);
            }
            $this->putf('/Mask [%s]', $trns);
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
            /** @phpstan-var PdfImageType $info */
            $info = [
                'w' => $info['w'],
                'h' => $info['h'],
                'cs' => 'DeviceGray',
                'bpc' => 8,
                'f' => $info['f'] ?? '',
                'dp' => $dp,
                'data' => $info['smask'],
            ];
            $this->putImage($info);
        }
        // Palette
        if ('Indexed' === $info['cs']) {
            $this->putStreamObject($info['pal']);
        }
    }

    /**
     * Put images to this buffer.
     */
    protected function putImages(): void
    {
        foreach (\array_keys($this->images) as $file) {
            $this->putImage($this->images[$file]);
            unset($this->images[$file]['data']);
            unset($this->images[$file]['smask']);
        }
    }

    /**
     * Put medata to this buffer.
     */
    protected function putInfo(): void
    {
        $date = \date('YmdHisO', $this->creationDate);
        $value = \sprintf("D:%s'%s'", \substr($date, 0, -2), \substr($date, -2));
        $this->addMetaData('CreationDate', $value);
        foreach ($this->metadata as $key => $value) {
            $this->putf('/%s %s', $key, $this->textString($value));
        }
    }

    /**
     * Put links to this buffer.
     */
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
            $output = \sprintf('<</Type /Annot /Subtype /Link /Rect [%s] /Border [0 0 0] ', $rect);
            if (\is_string($pageLink[4])) {
                $output .= \sprintf('/A <</S /URI /URI %s>>>>', $this->textString($pageLink[4]));
            } else {
                $link = $this->links[$pageLink[4]];
                $index = (int) $link[0];
                $pageInfo = $this->pageInfos[$index] ?? [];
                if (isset($pageInfo['size'])) {
                    $height = $pageInfo['size'][1];
                } else {
                    $height = (PdfOrientation::PORTRAIT === $this->defaultOrientation)
                        ? $this->defaultPageSize[1] * $this->scaleFactor
                        : $this->defaultPageSize[0] * $this->scaleFactor;
                }
                $output .= \sprintf(
                    '/Dest [%d 0 R /XYZ 0 %.2F null]>>',
                    $pageInfo['n'] ?? 0,
                    $height - (float) $link[1] * $this->scaleFactor
                );
            }
            $this->put($output);
            $this->endObj();
        }
    }

    /**
     * Put page to this buffer.
     */
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
            $output = '/Annots [';
            foreach ($this->pageLinks[$n] as $pageLink) {
                $output .= \sprintf('%d 0 R ', $pageLink[5]);
            }
            $output .= ']';
            $this->put($output);
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
     * Put pages to this buffer.
     *
     * @psalm-suppress InvalidPropertyAssignmentValue
     */
    protected function putPages(): void
    {
        $page = $this->page;
        $n = $this->objectNumber;
        for ($i = 1; $i <= $page; ++$i) {
            $this->pageInfos[$i]['n'] = ++$n;
            ++$n;
            foreach ($this->pageLinks[$i] as &$pageLink) {
                $pageLink[5] = ++$n;
            }
        }
        for ($i = 1; $i <= $page; ++$i) {
            $this->putPage($i);
        }
        // Pages root
        $this->newObj(1);
        $this->put('<</Type /Pages');
        $kids = '/Kids [';
        for ($i = 1; $i <= $page; ++$i) {
            $kids .= \sprintf('%d 0 R ', $this->pageInfos[$i]['n']);
        }
        $kids .= ']';
        $this->put($kids);
        $this->putf('/Count %d', $page);
        if (PdfOrientation::PORTRAIT === $this->defaultOrientation) {
            $width = $this->defaultPageSize[0];
            $height = $this->defaultPageSize[1];
        } else {
            $width = $this->defaultPageSize[1];
            $height = $this->defaultPageSize[0];
        }
        $this->putf('/MediaBox [0 0 %.2F %.2F]', $width * $this->scaleFactor, $height * $this->scaleFactor);
        $this->put('>>');
        $this->endObj();
    }

    /**
     * Put resource dictionary to this buffer.
     */
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
     * Put resources dictionary to this buffer.
     *
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

    /**
     * Put stream string to this buffer.
     */
    protected function putStream(string $data): void
    {
        $this->put('stream');
        $this->put($data);
        $this->put('endstream');
    }

    /**
     * Put stream object to this buffer.
     */
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
        $this->putf('<<%s>>', $entries);
        $this->putStream($data);
        $this->endObj();
    }

    /**
     * Put trailer to this buffer.
     */
    protected function putTrailer(): void
    {
        $this->putf('/Size %d', $this->objectNumber + 1);
        $this->putf('/Root %d 0 R', $this->objectNumber);
        $this->putf('/Info %d 0 R', $this->objectNumber - 1);
    }

    /**
     * Put X object dictionary images to this buffer.
     */
    protected function putXObjectDictionary(): void
    {
        foreach ($this->images as $image) {
            $this->putf('/I%d %d 0 R', $image['i'], $image['n']);
        }
    }

    /**
     * Read an integer from the given stream.
     *
     * @param resource $stream
     *
     * @throws PdfException
     */
    protected function readInt($stream): int
    {
        // Read a 4-byte integer from stream
        /** @phpstan-var array{i: int} $a */
        $a = (array) \unpack('Ni', $this->readStream($stream, 4));

        return $a['i'];
    }

    /**
     * Read a string from the given stream.
     *
     * @phpstan-param resource $stream
     *
     * @throws PdfException
     */
    protected function readStream($stream, int $len): string
    {
        // Read n bytes from stream
        $result = '';
        while ($len > 0 && !\feof($stream)) {
            $str = \fread($stream, $len);
            if (!\is_string($str)) {
                throw new PdfException('Error while reading stream.');
            }
            $len -= \strlen($str);
            $result .= $str;
        }
        if ($len > 0) {
            throw new PdfException('Unexpected end of stream.');
        }

        return $result;
    }

    /**
     * Convert the given string.
     */
    protected function textString(string $str): string
    {
        // Format a text string
        if (!$this->isAscii($str)) {
            $str = $this->convertUtf8ToUtf16($str);
        }

        return '(' . $this->escape($str) . ')';
    }

    /**
     * Convert the array to Unicode C map.
     *
     * @phpstan-param UvType $uv
     */
    protected function toUnicodeCmap(array $uv): string
    {
        $nbc = 0;
        $nbr = 0;
        $chars = '';
        $ranges = '';
        foreach ($uv as $c => $v) {
            if (\is_array($v)) {
                $ranges .= \sprintf("<%02X> <%02X> <%04X>\n", $c, $c + $v[1] - 1, $v[0]);
                ++$nbr;
            } else {
                $chars .= \sprintf("<%02X> <%04X>\n", $c, $v);
                ++$nbc;
            }
        }
        $output = "/CIDInit /ProcSet findresource begin\n";
        $output .= "12 dict begin\n";
        $output .= "begincmap\n";
        $output .= "/CIDSystemInfo\n";
        $output .= "<</Registry (Adobe)\n";
        $output .= "/Ordering (UCS)\n";
        $output .= "/Supplement 0\n";
        $output .= ">> def\n";
        $output .= "/CMapName /Adobe-Identity-UCS def\n";
        $output .= "/CMapType 2 def\n";
        $output .= "1 begincodespacerange\n";
        $output .= "<00> <FF>\n";
        $output .= "endcodespacerange\n";
        if ($nbr > 0) {
            $output .= \sprintf("%d beginbfrange\n", $nbr);
            $output .= $ranges;
            $output .= "endbfrange\n";
        }
        if ($nbc > 0) {
            $output .= \sprintf("%d beginbfchar\n", $nbc);
            $output .= $chars;
            $output .= "endbfchar\n";
        }
        $output .= "endcmap\n";
        $output .= "CMapName currentdict /CMap defineresource pop\n";
        $output .= "end\n";

        return $output . 'end';
    }
}
