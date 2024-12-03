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

use fpdf\Enums\PdfDestination;
use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfLayout;
use fpdf\Enums\PdfLineCap;
use fpdf\Enums\PdfLineJoin;
use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfOrientation;
use fpdf\Enums\PdfPageMode;
use fpdf\Enums\PdfPageSize;
use fpdf\Enums\PdfRectangleStyle;
use fpdf\Enums\PdfRotation;
use fpdf\Enums\PdfState;
use fpdf\Enums\PdfTextAlignment;
use fpdf\Enums\PdfUnit;
use fpdf\Enums\PdfVersion;
use fpdf\Enums\PdfZoom;
use fpdf\Interfaces\PdfColorInterface;
use fpdf\Interfaces\PdfImageParserInterface;

/**
 * Represent a PDF document.
 *
 * @phpstan-type ColorType = int<0, 255>
 * @phpstan-type UvType = array<int, int|int[]>
 * @phpstan-type FontType = array{
 *     index: int,
 *     number: int,
 *     type: string,
 *     name: string,
 *     path: string,
 *     file?: string,
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
 * @phpstan-type FontFileType = array{
 *     number: int,
 *     length1: int,
 *     length2?: int}
 * @phpstan-type PageInfoType = array{
 *     number: int,
 *     rotation?: PdfRotation,
 *     size?: PdfSize}
 * @phpstan-type ImageType = array{
 *     index: int,
 *     number: int,
 *     width: int,
 *     height: int,
 *     color_space: string,
 *     bits_per_component: int,
 *     filter?: string,
 *     data?: string,
 *     decode_parms?: string,
 *     soft_mask?: string,
 *     palette: string,
 *     transparencies?: int[]}
 * @phpstan-type PageLinkType = array{
 *     0: float,
 *     1: float,
 *     2: float,
 *     3: float,
 *     4: string|int,
 *     5: int}
 * @phpstan-type LinkType = array{
 *     0: int,
 *     1: float}
 */
class PdfDocument
{
    /**
     * The default line height in mm.
     */
    final public const LINE_HEIGHT = 5.0;

    /**
     * The FPDF version.
     */
    final public const VERSION = '2.0';

    // the new line separator
    private const NEW_LINE = "\n";

    /** The alias for the total number of pages. */
    protected string $aliasNumberPages = '{nb}';
    /** Indicates whether the alpha channel is used. */
    protected bool $alphaChannel = false;
    /** The automatic page breaking. */
    protected bool $autoPageBreak = false;
    /** The bottom margin in user unit (page break margin). */
    protected float $bottomMargin = 0.0;
    /** The buffer holding in-memory PDF. */
    protected string $buffer = '';
    /** The cell margin in the user unit. */
    protected float $cellMargin = 0.0;
    /**
     * The map character codes to character glyphs.
     *
     * @phpstan-var array<string, int>
     */
    protected array $charMaps = [];
    /** Indicates whether fill and text colors are different. */
    protected bool $colorFlag = false;
    /** The compression flag. */
    protected bool $compression = true;
    /**
     * The current font info.
     *
     * @phpstan-var FontType|null array
     */
    protected ?array $currentFont = null;
    /** The current orientation. */
    protected PdfOrientation $currentOrientation;
    /** The current page size. */
    protected PdfSize $currentPageSize;
    /** The current page size in point. */
    protected PdfSize $currentPageSizeInPoint;
    /** The current page rotation. */
    protected PdfRotation $currentRotation = PdfRotation::DEFAULT;
    /** The default orientation. */
    protected PdfOrientation $defaultOrientation;
    /** The default page size.  */
    protected PdfSize $defaultPageSize;
    /** The drawing color. */
    protected ?PdfColorInterface $drawColor = null;
    /**
     * The encodings.
     *
     * @phpstan-var array<string, int>
     */
    protected array $encodings = [];
    /** The filling color. */
    protected ?PdfColorInterface $fillColor = null;
    /** The current font family. */
    protected string $fontFamily = '';
    /**
     * The font files.
     *
     * @phpstan-var array<string, FontFileType>
     */
    protected array $fontFiles = [];
    /** The directory containing fonts. */
    protected string $fontPath = '';
    /**
     * The used fonts.
     *
     * @phpstan-var array<string, FontType>
     */
    protected array $fonts = [];
    /** The current font size in user unit. */
    protected float $fontSize = 0.0;
    /** The current font size in points. */
    protected float $fontSizeInPoint = 9.0;
    /** The current font style. */
    protected PdfFontStyle $fontStyle = PdfFontStyle::REGULAR;
    /** The current page height in user unit. */
    protected float $height = 0.0;
    /**
     * Used images.
     *
     * @phpstan-var array<string, ImageType>
     */
    protected array $images = [];
    /** The flag set when processing footer. */
    protected bool $inFooter = false;
    /**The flag set when processing the header. */
    protected bool $inHeader = false;
    /** The height of the last printed cell. */
    protected float $lastHeight = 0.0;
    /** The layout display mode. */
    protected PdfLayout $layout;
    /** The left margin in the user unit. */
    protected float $leftMargin = 0.0;
    /** The line cap. */
    protected PdfLineCap $lineCap;
    /** The line join. */
    protected PdfLineJoin $lineJoin;
    /** The line width in the user unit. */
    protected float $lineWidth = 0.0;
    /**
     * The internal links.
     *
     * @phpstan-var array<int, LinkType>
     */
    protected array $links = [];
    /**
     * The document properties.
     *
     * @phpstan-var array<string, string>
     */
    protected array $metadata = [];
    /** The current object number. */
    protected int $objectNumber = 2;
    /**
     * The object offsets.
     *
     * @phpstan-var array<int, int>
     */
    protected array $offsets = [];
    /** The current page number. */
    protected int $page = 0;
    /** The threshold used to trigger page breaks. */
    protected float $pageBreakTrigger = 0.0;
    /**
     * The page-related data.
     *
     * @phpstan-var array<int, PageInfoType>
     */
    protected array $pageInfos = [];
    /**
     * The links in pages.
     *
     * @phpstan-var array<int, PageLinkType[]>
     */
    protected array $pageLinks = [];
    /** The displayed page mode */
    protected PdfPageMode $pageMode;
    /**
     * The pages.
     *
     * @phpstan-var array<int, string>
     */
    protected array $pages = [];
    /** The PDF version number. */
    protected PdfVersion $pdfVersion;
    /** The right margin in the user unit. */
    protected float $rightMargin = 0.0;
    /** The scale factor (number of points in user unit). */
    protected float $scaleFactor = 1.0;
    /** The current document state. */
    protected PdfState $state = PdfState::NO_PAGE;
    /** The text color. */
    protected ?PdfColorInterface $textColor = null;
    /** The document title. */
    protected string $title = '';
    /** The top margin in user unit. */
    protected float $topMargin = 0.0;
    /** The underlining flag. */
    protected bool $underline = false;
    /** The viewer preferences */
    protected PdfViewerPreferences $viewerPreferences;
    /** The current page width in user unit. */
    protected float $width = 0.0;
    /** The word spacing. */
    protected float $wordSpacing = 0.0;
    /** The current x position in user unit. */
    protected float $x = 0.0;
    /** The current y position in user unit. */
    protected float $y = 0.0;
    /** The zoom display mode. */
    protected PdfZoom|int $zoom;

    /**
     * Create a new instance.
     *
     * It allows setting up the page orientation, the page size, and the unit of measure used in all methods
     * (except for font sizes).
     *
     * @param PdfOrientation      $orientation the page orientation
     * @param PdfUnit             $unit        the document unit to use
     * @param PdfPageSize|PdfSize $size        the page size
     */
    public function __construct(
        PdfOrientation $orientation = PdfOrientation::PORTRAIT,
        protected PdfUnit $unit = PdfUnit::MILLIMETER,
        PdfPageSize|PdfSize $size = PdfPageSize::A4
    ) {
        // enumerations
        $this->layout = PdfLayout::getDefault();
        $this->lineCap = PdfLineCap::getDefault();
        $this->lineJoin = PdfLineJoin::getDefault();
        $this->pageMode = PdfPageMode::getDefault();
        $this->zoom = PdfZoom::getDefault();

        // scale factor
        $this->scaleFactor = $unit->getScaleFactor();
        // font path
        // @phpstan-ignore cast.string
        $this->fontPath = \defined('FPDF_FONTPATH') ? (string) FPDF_FONTPATH : __DIR__ . '/font/';
        // font size
        $this->fontSize = $this->fontSizeInPoint / $this->scaleFactor;
        // page orientation
        $this->defaultOrientation = $orientation;
        $this->currentOrientation = $orientation;
        // page size
        $size = $this->getPageSize($size);
        $this->defaultPageSize = clone $size;
        $this->currentPageSize = clone $size;
        if (PdfOrientation::PORTRAIT === $orientation) {
            $this->width = $size->width;
            $this->height = $size->height;
        } else {
            $this->width = $size->height;
            $this->height = $size->width;
        }
        $this->currentPageSizeInPoint = PdfSize::instance($this->width, $this->height)->scale($this->scaleFactor);
        // page margins (1 cm)
        $margin = 28.35 / $this->scaleFactor;
        $this->setMargins($margin, $margin);
        // interior cell margin (1 mm)
        $this->cellMargin = $margin / 10.0;
        // line width (0.2 mm)
        $this->lineWidth = .567 / $this->scaleFactor;
        // automatic page break
        $this->setAutoPageBreak(true, 2.0 * $margin);
        // producer
        $this->setProducer('FPDF2 ' . self::VERSION);
        // version
        $this->pdfVersion = PdfVersion::getDefault();
        // preferences
        $this->viewerPreferences = new PdfViewerPreferences();
    }

    /**
     * Imports a TrueType, an OpenType, or a Type1 font and makes it available.
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
     * @param ?string            $file   The name of the font definition file. By default, it is built from the family
     *                                   and style, in lower case with no space.
     * @param ?string            $dir    The directory where to load the definition file. If not specified, the default
     *                                   directory will be used.
     *
     * @throws PdfException if the font file does not exit or if the font definition is incorrect
     *
     * @see PdfDocument::setFont()
     * @see PdfDocument::setFontSizeInPoint()
     */
    public function addFont(
        PdfFontName|string $family,
        PdfFontStyle $style = PdfFontStyle::REGULAR,
        ?string $file = null,
        ?string $dir = null
    ): static {
        if ($family instanceof PdfFontName) {
            $family = $family->value;
        }
        $family = \strtolower($family);
        $file ??= \str_replace(' ', '', $family) . $style->value . '.php';
        $fontKey = $family . $style->value;
        if (isset($this->fonts[$fontKey])) {
            return $this;
        }
        if (\str_contains($file, '/') || \str_contains($file, '\\')) {
            throw PdfException::format('Incorrect font definition file name: %s.', $file);
        }
        $dir ??= $this->fontPath;
        if (!\str_ends_with($dir, '/') && !\str_ends_with($dir, '\\')) {
            $dir .= \DIRECTORY_SEPARATOR;
        }
        $info = $this->loadFont($dir . $file);
        $info['index'] = \count($this->fonts) + 1;
        if (isset($info['file']) && '' !== $info['file']) {
            // Embedded font
            $key = $dir . $info['file'];
            $info['file'] = $key;
            if ('TrueType' === $info['type']) {
                $this->fontFiles[$key] = [
                    'number' => 0,
                    'length1' => $info['originalsize'],
                ];
            } else {
                $this->fontFiles[$key] = [
                    'number' => 0,
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
     * destination is defined with <code>setLink()</code>.
     *
     * @return int the link identifier
     *
     * @see PdfDocument::link()
     * @see PdfDocument::setLink()
     * @see PdfDocument::createLink()
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
     * page is added, the current position set to the top-left corner according to the left and top margins and
     * <code>header()</code> is called to output the header. The font, which was set before calling, is automatically
     * restored. There is no need to call <code>setFont()</code> again if you want to continue with the same font. The
     * same is <code>true</code> for colors and line width.
     *
     * The origin of the coordinate system is in the top-left corner, and increasing ordinates go downwards.
     *
     * @param ?PdfOrientation          $orientation the page orientation or <code>null</code> to use the current
     *                                              orientation
     * @param PdfPageSize|PdfSize|null $size        the page size or <code>null</code> to use the current size
     * @param ?PdfRotation             $rotation    the rotation by which to rotate the page or <code>null</code> to
     *                                              use the current rotation
     *
     * @throws PdfException if the document is closed
     */
    public function addPage(
        ?PdfOrientation $orientation = null,
        PdfPageSize|PdfSize|null $size = null,
        ?PdfRotation $rotation = null
    ): static {
        if (PdfState::CLOSED === $this->state) {
            throw PdfException::instance('The document is closed.');
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

        // page footer and close page
        if ($this->page > 0) {
            $this->inFooter = true;
            $this->footer();
            $this->inFooter = false;
            $this->endPage();
        }
        // start the new page
        $this->beginPage($orientation, $size, $rotation);

        // set line cap and line join
        $this->outLineCap();
        if (!$this->lineJoin->isDefault()) {
            $this->outLineJoin();
        }

        // restore context
        $this->lineWidth = $lineWidth;
        $this->outLineWidth();
        if ('' !== $fontFamily) {
            $this->setFont($fontFamily, $fontStyle, $fontSizeInPoint);
        }
        $this->drawColor = $drawColor;
        $this->outColor($this->drawColor, true);

        $this->fillColor = $fillColor;
        $this->outColor($this->fillColor, false);

        $this->textColor = $textColor;
        $this->colorFlag = $colorFlag;

        // page header
        $this->inHeader = true;
        $this->header();
        $this->inHeader = false;

        // restore context
        if ($this->lineWidth !== $lineWidth) {
            $this->lineWidth = $lineWidth;
            $this->outLineWidth();
        }
        if ('' !== $fontFamily) {
            $this->setFont($fontFamily, $fontStyle, $fontSizeInPoint);
        }
        if ($this->drawColor !== $drawColor) {
            $this->drawColor = $drawColor;
            $this->outColor($this->drawColor, true);
        }
        if ($this->fillColor !== $fillColor) {
            $this->fillColor = $fillColor;
            $this->outColor($this->fillColor, false);
        }
        $this->textColor = $textColor;
        $this->colorFlag = $colorFlag;

        return $this;
    }

    /**
     * Prints a cell (rectangular area) with optional borders, background color, and character string.
     *
     * It is possible to put a link on the cell.
     *
     * @param ?float           $width  the cell width. If <code>null</code>, the cell extends up to the right margin.
     * @param float            $height the cell height
     * @param string           $text   the cell text
     * @param ?PdfBorder       $border indicates how borders must be drawn around the cell. If <code>null</code>, or
     *                                 <code>none()</code>, no border is drawn.
     * @param PdfMove          $move   indicates where the current position should go after the call
     * @param PdfTextAlignment $align  the text alignment
     * @param bool             $fill   indicates if the cell background must be painted (<code>true</code>) or
     *                                 transparent (<code>false</code>)
     * @param string|int|null  $link   a URL or an identifier returned by <code>addLink()</code>
     *
     * @throws PdfException if the given text is not empty and no font is set
     *
     * @see PdfDocument::multiCell()
     */
    public function cell(
        ?float $width = null,
        float $height = self::LINE_HEIGHT,
        string $text = '',
        ?PdfBorder $border = null,
        PdfMove $move = PdfMove::RIGHT,
        PdfTextAlignment $align = PdfTextAlignment::LEFT,
        bool $fill = false,
        string|int|null $link = null
    ): static {
        $scaleFactor = $this->scaleFactor;
        if (!$this->isPrintable($height) && !$this->inHeader && !$this->inFooter && $this->autoPageBreak) {
            // automatic page break
            $x = $this->x;
            $wordSpacing = $this->wordSpacing;
            if ($wordSpacing > 0) {
                $this->updateWordSpacing();
            }
            $this->addPage($this->currentOrientation, $this->currentPageSize, $this->currentRotation);
            $this->x = $x;
            if ($wordSpacing > 0) {
                $this->updateWordSpacing($wordSpacing);
            }
        }

        $output = '';
        $width ??= $this->getRemainingWidth();
        $border ??= PdfBorder::none();
        if ($fill || $border->isAll()) {
            if ($fill) {
                $style = $border->isAll() ? PdfRectangleStyle::BOTH : PdfRectangleStyle::FILL;
            } else {
                $style = PdfRectangleStyle::BORDER;
            }
            $output .= \sprintf(
                '%.2F %.2F %.2F %.2F re %s ',
                $this->x * $scaleFactor,
                ($this->height - $this->y) * $scaleFactor,
                $width * $scaleFactor,
                -$height * $scaleFactor,
                $style->value
            );
        }
        if ('' === $output && $border->isAny()) {
            $output = $this->formatBorders($this->x, $this->y, $width, $height, $border);
        }
        $text = $this->cleanText($text);
        if ('' !== $text) {
            if (null === $this->currentFont) {
                throw PdfException::instance('No font has been set.');
            }
            $dx = match ($align) {
                PdfTextAlignment::RIGHT => $width - $this->cellMargin - $this->getStringWidth($text),
                PdfTextAlignment::CENTER => ($width - $this->getStringWidth($text)) / 2.0,
                default => $this->cellMargin,
            };
            if ($this->colorFlag && $this->textColor instanceof PdfColorInterface) {
                $output .= \sprintf('q %s ', $this->formatColor($this->textColor, true));
            }
            $output .= \sprintf(
                'BT %.2F %.2F Td (%s) Tj ET',
                ($this->x + $dx) * $scaleFactor,
                ($this->height - ($this->y + 0.5 * $height + 0.3 * $this->fontSize)) * $scaleFactor,
                $this->escape($text)
            );
            if ($this->underline) {
                $output .= $this->doUnderline(
                    $this->x + $dx,
                    $this->y + 0.5 * $height + 0.3 * $this->fontSize,
                    $text
                );
            }
            if ($this->colorFlag) {
                $output .= ' Q';
            }
            if (self::isLink($link)) {
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
     */
    public function close(): static
    {
        if (PdfState::CLOSED === $this->state) {
            return $this;
        }
        if (0 === $this->page) {
            $this->addPage();
        }
        // page footer
        $this->inFooter = true;
        $this->footer();
        $this->inFooter = false;
        // close page and document
        $this->endPage();
        $this->endDoc();

        return $this;
    }

    /**
     * Creates a new internal link for the given position and page and returns its identifier.
     *
     * This is a combination of the <code>addLink()</code> and the <code>setLink()</code> functions.
     *
     * @param float|null $y    the ordinate of the target position. A <code>null</code> value indicates the current
     *                         ordinate.
     *                         The default value is 0 (top of the page).
     * @param int|null   $page the target page or <code>null</code> to use the current page
     *
     * @return int the link identifier
     *
     * @see PdfDocument::addLink()
     * @see PdfDocument::setLink()
     */
    public function createLink(?float $y = 0, ?int $page = null): int
    {
        $id = $this->addLink();
        $this->setLink($id, $y, $page);

        return $id;
    }

    /**
     * This method is used to render the page footer.
     *
     * It is automatically called before <code>addPage()</code> and <code>close()</code> methods and should not be
     * called directly by the application.
     *
     * The implementation is empty, so you have to subclass it and override the method if you want specific
     * processing.
     *
     * @see PdfDocument::header()
     */
    public function footer(): void
    {
    }

    /**
     * Gets the bottom margin in the user unit.
     *
     * The default value is 10 mm.
     */
    public function getBottomMargin(): float
    {
        return $this->bottomMargin;
    }

    /**
     * Gets the cell margin in user unit.
     *
     * The default value is 1 mm.
     */
    public function getCellMargin(): float
    {
        return $this->cellMargin;
    }

    /**
     * Gets the current font size in the user unit.
     */
    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    /**
     * Gets the current font size in points.
     */
    public function getFontSizeInPoint(): float
    {
        return $this->fontSizeInPoint;
    }

    /**
     * Gets the height of the last printed cell.
     *
     * @return float the height or 0.0 if no printed cell
     */
    public function getLastHeight(): float
    {
        return $this->lastHeight;
    }

    /**
     * Gets the display layout.
     */
    public function getLayout(): PdfLayout
    {
        return $this->layout;
    }

    /**
     * Gets the left margin in user unit.
     *
     * The default value is 10 mm.
     */
    public function getLeftMargin(): float
    {
        return $this->leftMargin;
    }

    /**
     * Gets the line cap.
     *
     * The default value is <code>PdfLineCap::SQUARE</code>.
     */
    public function getLineCap(): PdfLineCap
    {
        return $this->lineCap;
    }

    /**
     * Gets the line join style.
     *
     *  The default value is <code>PdfLineJoin::MITER</code>.
     */
    public function getLineJoin(): PdfLineJoin
    {
        return $this->lineJoin;
    }

    /**
     * Gets the number of lines to use for the given text and width.
     *
     * Computes the number of lines a <code>multiCell()</code> of the given width will take.
     *
     * @param ?string $text       the text to compute
     * @param ?float  $width      the desired width. If <code>null</code>, the width extends up to the right margin.
     * @param ?float  $cellMargin the desired cell margin or <code>null</code> to use the current value
     *
     * @return int the number of lines
     *
     * @throws PdfException if the given text is not empty and no font is set
     */
    public function getLinesCount(?string $text, ?float $width = null, ?float $cellMargin = null): int
    {
        if (null === $text || '' === $text) {
            return 0;
        }
        $text = $this->cleanText($text);
        $len = \strlen($text);
        if (0 === $len) {
            return 0;
        }

        if (null === $this->currentFont) {
            throw PdfException::instance('No font has been set.');
        }

        $index = 0;
        $lastIndex = 0;
        $sepIndex = -1;
        $linesCount = 1;
        $currentWidth = 0.0;
        $cellMargin ??= $this->cellMargin;
        $width ??= $this->getRemainingWidth();
        $charWidths = $this->currentFont['cw'];
        $maxWidth = ($width - 2.0 * $cellMargin) * 1000.0 / $this->fontSize;

        while ($index < $len) {
            $ch = $text[$index];
            if (self::NEW_LINE === $ch) {
                // explicit line break
                ++$index;
                $sepIndex = -1;
                $lastIndex = $index;
                $currentWidth = 0.0;
                ++$linesCount;
                continue;
            }
            if (' ' === $ch) {
                $sepIndex = $index;
            }
            $currentWidth += (float) $charWidths[$ch];
            if ($currentWidth > $maxWidth) {
                // automatic line break
                if (-1 === $sepIndex) {
                    if ($index === $lastIndex) {
                        ++$index;
                    }
                } else {
                    $index = $sepIndex + 1;
                }
                $sepIndex = -1;
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
     * Gets the line width.
     */
    public function getLineWidth(): float
    {
        return $this->lineWidth;
    }

    /**
     * Gets the current page number.
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Gets the current page height in user unit.
     */
    public function getPageHeight(): float
    {
        return $this->height;
    }

    /**
     * Gets how the document shall be displayed when opened.
     */
    public function getPageMode(): PdfPageMode
    {
        return $this->pageMode;
    }

    /**
     * Gets the current page width in the user unit.
     */
    public function getPageWidth(): float
    {
        return $this->width;
    }

    /**
     * Gets the PDF version.
     */
    public function getPdfVersion(): PdfVersion
    {
        return $this->pdfVersion;
    }

    /**
     * Gets the current X and Y position in the user unit.
     */
    public function getPosition(): PdfPoint
    {
        return new PdfPoint($this->x, $this->y);
    }

    /**
     * Gets the printable width in the user unit.
     *
     * @return float the page width minus the left and right margins
     */
    public function getPrintableWidth(): float
    {
        return $this->width - $this->leftMargin - $this->rightMargin;
    }

    /**
     * Gets the remaining printable width in the user unit.
     *
     * @return float the value from the current abscissa (x) to the right margin
     */
    public function getRemainingWidth(): float
    {
        return $this->width - $this->rightMargin - $this->x;
    }

    /**
     * Gets the right margin in the user unit.
     *
     * The default value is 10 mm.
     */
    public function getRightMargin(): float
    {
        return $this->rightMargin;
    }

    /**
     * Gets the document state.
     */
    public function getState(): PdfState
    {
        return $this->state;
    }

    /**
     * Gets the length of a string in the user unit.
     *
     * @param string $str the string to get width for
     *
     * @return float the string width or 0.0 if the string is empty or if no font is set
     */
    public function getStringWidth(string $str): float
    {
        if (null === $this->currentFont || '' === $str) {
            return 0.0;
        }
        $str = $this->cleanText($str);
        if ('' === $str) {
            return 0.0;
        }

        $width = 0.0;
        $charWidths = $this->currentFont['cw'];
        for ($i = 0,$len = \strlen($str); $i < $len; ++$i) {
            $width += (float) $charWidths[$str[$i]];
        }

        return $width * $this->fontSize / 1000.0;
    }

    /**
     * Gets the document title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Gets the top margin in user unit.
     *
     * The default value is 10 mm.
     */
    public function getTopMargin(): float
    {
        return $this->topMargin;
    }

    /**
     * Gets the viewer preferences.
     */
    public function getViewerPreferences(): PdfViewerPreferences
    {
        return $this->viewerPreferences;
    }

    /**
     * Gets the abscissa of the current position in the user unit.
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * Gets the ordinate of the current position in the user unit.
     */
    public function getY(): float
    {
        return $this->y;
    }

    /**
     * Gets the zoom.
     */
    public function getZoom(): PdfZoom|int
    {
        return $this->zoom;
    }

    /**
     * This method is used to render the page header.
     *
     * It is automatically called after <code>addPage()</code> method and should not be called directly by the
     * application.
     *
     * The implementation is empty, so you have to subclass it and override the method if you want specific
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
     * @param float|null $lineWidth   the optional line width or <code>null</code> to use the current line width
     */
    public function horizontalLine(float $beforeSpace = 1.0, float $afterSpace = 1.0, ?float $lineWidth = null): static
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
     * <li>One explicit dimension, the other being calculated automatically to keep the original
     * proportions.</li>
     * <li>No explicit dimension, in which case the image is put at 96 dpi.</li>
     * </ul>
     *
     * Supported formats are JPEG, PNG, and GIF. The GD extension is required for the GIF format.
     *
     * For the JPEG format, the following variants are allowed:
     * <ul>
     * <li>Gray scales.</li>
     * <li>True colors (24 bits).</li>
     * <li>CMYK (32 bits).</li>
     * </ul>
     *
     * For the PNG format, are allowed:
     * <ul>
     * <li>Gray scales on at most 8 bits (256 levels).</li>
     * <li>Indexed colors.</li>
     * <li>True colors (24 bits).</li>
     * </ul>
     *
     * For the GIF format:
     * <ul>
     * <li>For an animated GIF, only the first frame is displayed.</li>
     * </ul>
     *
     * For all:
     * <ul>
     * <li>Transparency is supported.</li>
     * <li>The format can be specified explicitly or inferred from the file extension.</li>
     * <li>It is possible to put a link on the image.</li>
     * </ul>
     *
     * It is possible to put a link on the image.
     *
     * <b>Remark: </b>If an image is used several times, only one copy is embedded in the file.
     *
     * @param string          $file   the path or the URL of the image
     * @param ?float          $x      the abscissa of the upper-left corner. If <code>null</code>, the current abscissa
     *                                is used.
     * @param ?float          $y      the ordinate of the upper-left corner. If <code>null</code>, the current
     *                                ordinate is used. Moreover, a page break is triggered first if necessary (in case
     *                                automatic page breaking is enabled). After the call, the current ordinate
     *                                is moved to the bottom of the image.
     * @param float           $width  the width of the image in the page. There are three cases:
     *                                <ul>
     *                                <li>If the value is positive, it represents the width in user unit.</li>
     *                                <li>If the value is negative, the absolute value represents the horizontal
     *                                resolution in dpi.</li>
     *                                <li>If the value is not specified or equal to zero, it is automatically
     *                                calculated.</li>
     *                                </ul>
     * @param float           $height the height of the image in the page. There are three cases:
     *                                <ul>
     *                                <li>If the value is positive, it represents the width in user unit.</li>
     *                                <li>If the value is negative, the absolute value represents the horizontal
     *                                resolution in dpi.</li>
     *                                <li>If the value is not specified or equal to zero, it is automatically
     *                                calculated.</li>
     *                                </ul>
     * @param string          $type   the image format. If not specified, the type is inferred from the file extension.
     * @param string|int|null $link   an URL or an identifier returned by <code>addLink()</code>
     *
     * @throws PdfException if the image file is empty, or if the image cannot be processed
     */
    public function image(
        string $file,
        ?float $x = null,
        ?float $y = null,
        float $width = 0.0,
        float $height = 0.0,
        string $type = '',
        string|int|null $link = null
    ): static {
        if ('' === $file) {
            throw PdfException::instance('Image file name is empty.');
        }
        if (!isset($this->images[$file])) {
            // first use of this image, get info
            if ('' === $type) {
                $type = \pathinfo($file, \PATHINFO_EXTENSION);
                if ('' === $type) {
                    throw PdfException::format('Image file has no extension and no type was specified: %s.', $file);
                }
            }
            $type = \strtolower($type);
            $parser = $this->getImageParser($type);
            if (!$parser instanceof PdfImageParserInterface) {
                throw PdfException::format('Unsupported image type: %s.', $type);
            }
            $image = $parser->parse($this, $file);
            $image['index'] = \count($this->images) + 1;
            $this->images[$file] = $image;
        } else {
            $image = $this->images[$file];
        }

        // automatic width and height calculation if needed
        if (0.0 === $width && 0.0 === $height) {
            // Put image at 96 dpi
            $width = -96.0;
            $height = -96.0;
        }

        $infoWidth = (float) $image['width'];
        $infoHeight = (float) $image['height'];
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

        // flowing mode
        if (null === $y) {
            if (!$this->isPrintable($height) && !$this->inHeader && !$this->inFooter && $this->autoPageBreak) {
                // automatic page break
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
            ($this->height - $y - $height) * $this->scaleFactor,
            $image['index']
        );
        if (self::isLink($link)) {
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
     *
     * @see PdfDocument::setAutoPageBreak()
     */
    public function isAutoPageBreak(): bool
    {
        return $this->autoPageBreak;
    }

    /**
     * Returns if the given link is valid.
     *
     * To be valid, the link must be a non-empty string or a positive integer.
     *
     * @param string|int|null $link the link to validate
     *
     * @return bool <code>true</code> if valid
     *
     * @phpstan-assert-if-true (non-empty-string|positive-int) $link
     */
    public static function isLink(string|int|null $link): bool
    {
        return (\is_string($link) && '' !== $link) || (\is_int($link) && $link > 0);
    }

    /**
     * Returns if the given height does not cause an overflow (new page).
     *
     * @param float  $height the desired height
     * @param ?float $y      the ordinate position or <code>null</code> to use the current position
     *
     * @return bool <code>true</code> if printable within the current page; <code>false</code> if a new page is
     *              needed
     *
     * @see PdfDocument::addPage()
     */
    public function isPrintable(float $height, ?float $y = null): bool
    {
        return (($y ?? $this->y) + $height) <= $this->pageBreakTrigger;
    }

    /**
     * Draws a line between two points.
     *
     * Do nothing if points are equal.
     *
     * @param float $x1 the abscissa of the first point
     * @param float $y1 the ordinate of the first point
     * @param float $x2 the abscissa of the second point
     * @param float $y2 the ordinate of the second point
     */
    public function line(float $x1, float $y1, float $x2, float $y2): static
    {
        if ($x1 === $x2 && $y1 === $y2) {
            return $this;
        }

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
     * The current abscissa goes back to the left margin, and the ordinate increases by the amount passed parameter.
     *
     * @param float|null $height The height of the break. By default, the value equals the height of the last printed
     *                           cell.
     *
     * @see PdfDocument::setX()
     * @see PdfDocument::setY()
     * @see PdfDocument::setXY()
     */
    public function lineBreak(?float $height = null): static
    {
        $this->x = $this->leftMargin;
        $this->y += $height ?? $this->lastHeight;

        return $this;
    }

    /**
     * Draws a line between two points.
     *
     * Do nothing if points are equal.
     *
     * @param PdfPoint $start the abscissa and ordinate of the first point
     * @param PdfPoint $end   the abscissa and ordinate of the second point
     */
    public function linePoints(PdfPoint $start, PdfPoint $end): static
    {
        return $this->line($start->x, $start->y, $end->x, $end->y);
    }

    /**
     * Puts a link on a rectangular area of the page.
     *
     * Text or image links are generally put via <code>cell()</code>, <code>write()</code> or <code>image()</code>, but
     * this method can be useful, for instance, to define a clickable area inside an image.
     *
     * @param float      $x      the abscissa of the rectangle
     * @param float      $y      the ordinate of the rectangle
     * @param float      $width  the width of the rectangle
     * @param float      $height the height of the rectangle
     * @param string|int $link   an URL or an identifier returned by <code>addLink()</code>
     *
     * @see PdfDocument::addLink()
     * @see PdfDocument::setLink()
     * @see PdfDocument::createLink()
     */
    public function link(float $x, float $y, float $width, float $height, string|int $link): static
    {
        $heightInPoint = $this->currentPageSizeInPoint->height;
        $this->pageLinks[$this->page][] = [
            $x * $this->scaleFactor,
            $heightInPoint - $y * $this->scaleFactor,
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
     * @param bool  $resetX if <code>true</code>, the abscissa is reset to the left margin
     */
    public function moveY(float $delta, bool $resetX = true): static
    {
        if (0.0 !== $delta) {
            $this->setY($this->y + $delta, $resetX);
        }

        return $this;
    }

    /**
     * This method allows printing text with line breaks.
     *
     * They can be automatic as soon as the text reaches the right border of the cell, or explicit via the line
     * feed character ("\n"). As many cells as necessary are output, one below the other. Text can be aligned, centered,
     * or justified. The cell block can be framed and the background painted.
     *
     * @param ?float           $width  the cell width. If <code>null</code>, the cell extends up to the right margin.
     * @param float            $height the cell height
     * @param string           $text   the cell text
     * @param ?PdfBorder       $border indicates how borders must be drawn around the cell. If <code>null</code>, no
     *                                 border is drawn.
     * @param PdfTextAlignment $align  the text alignment
     * @param bool             $fill   indicates if the cell background must be painted (<code>true</code>) or
     *                                 transparent (<code>false</code>)
     *
     * @throws PdfException if the given text is not empty and no font is set
     *
     * @see PdfDocument::cell()
     */
    public function multiCell(
        ?float $width = null,
        float $height = self::LINE_HEIGHT,
        string $text = '',
        ?PdfBorder $border = null,
        PdfTextAlignment $align = PdfTextAlignment::JUSTIFIED,
        bool $fill = false
    ): static {
        if (null === $this->currentFont) {
            throw PdfException::instance('No font has been set.');
        }

        $charWidths = $this->currentFont['cw'];
        $width ??= $this->getRemainingWidth();
        $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;

        $border1 = PdfBorder::none();
        $border2 = PdfBorder::none();
        if ($border instanceof PdfBorder) {
            if ($border->isAll()) {
                $border1 = PdfBorder::all()->setBottom(false);
                $border2 = PdfBorder::leftRight();
            } else {
                $border1 = new PdfBorder($border->isLeft(), $border->isTop(), $border->isRight(), false);
                $border2 = new PdfBorder($border->isLeft(), false, $border->isRight(), false);
            }
        }

        $text = $this->cleanText($text);
        $len = \strlen($text);

        $index = 0;
        $newLine = 1;
        $lastIndex = 0;
        $sepIndex = -1;
        $newSeparator = 0;
        $lineSpace = 0.0;
        $currentWidth = 0.0;
        while ($index < $len) {
            $ch = $text[$index];
            if (self::NEW_LINE === $ch) {
                // explicit line break
                if ($this->wordSpacing > 0) {
                    $this->updateWordSpacing();
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
                if ($border instanceof PdfBorder && 2 === $newLine) {
                    $border1 = clone $border2;
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
                // automatic line break
                if (-1 === $sepIndex) {
                    if ($index === $lastIndex) {
                        ++$index;
                    }
                    if ($this->wordSpacing > 0) {
                        $this->updateWordSpacing();
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
                        $wordSpacing = ($newSeparator > 1)
                            ? ($widthMax - $lineSpace) / 1000.0 * $this->fontSize / (float) ($newSeparator - 1)
                            : 0;
                        $this->updateWordSpacing($wordSpacing, true);
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
                if ($border instanceof PdfBorder && 2 === $newLine) {
                    $border1 = clone $border2;
                }
            } else {
                ++$index;
            }
        }
        // last chunk
        if ($this->wordSpacing > 0) {
            $this->updateWordSpacing();
        }
        if ($border instanceof PdfBorder && $border->isBottom()) {
            $border1->setBottom();
        }
        $this->cell(
            $width,
            $height,
            \substr($text, $lastIndex, $index - $lastIndex),
            $border1,
            PdfMove::NEW_LINE,
            $align,
            $fill
        );

        return $this;
    }

    /**
     * Send the document to a given destination: browser, file or string.
     *
     * For a browser, the PDF viewer may be used or a download may be forced.
     * The method first calls <code>close()</code> if necessary to terminate the document.
     *
     * @param PdfDestination $destination The destination where to send the document
     * @param ?string        $name        The name of the file. It is ignored in case of
     *                                    destination <code>PdfDestination::STRING</code>. The default value
     *                                    is 'doc.pdf'.
     * @param bool           $isUTF8      Indicates if the name is encoded in ISO-8859-1 (<code>false</code>) or
     *                                    UTF-8 (<code>true</code>). Only used for destinations
     *                                    <code>PdfDestination::INLINE</code> and <code>PdfDestination::DOWNLOAD</code>.
     *
     * @return string the content if the output is <code>PdfDestination::STRING</code>, an empty string otherwise
     *
     * @throws PdfException if the destination is file, and the content cannot be written, or if some data has already
     *                      been output
     */
    public function output(
        PdfDestination $destination = PdfDestination::INLINE,
        ?string $name = null,
        bool $isUTF8 = false
    ): string {
        $this->close();
        $name ??= 'doc.pdf';
        switch ($destination) {
            case PdfDestination::INLINE:
                $this->checkOutput();
                if (\PHP_SAPI !== 'cli') {
                    $this->headers($name, $isUTF8, true);
                }
                echo $this->buffer;
                break;
            case PdfDestination::DOWNLOAD:
                $this->checkOutput();
                $this->headers($name, $isUTF8, false);
                echo $this->buffer;
                break;
            case PdfDestination::FILE:
                if (false === \file_put_contents($name, $this->buffer)) {
                    throw PdfException::format('Unable to create output file: %s.', $name);
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
     * If the dot per inch is equal to 0, return the pixels value.
     *
     * @param float|int $pixels the pixels to convert
     * @param float     $dpi    the dot per inch
     *
     * @return float the converted value as millimeters
     *
     * @see PdfDocument::pixels2UserUnit()
     * @see PdfDocument::points2UserUnit()
     */
    public function pixels2mm(float|int $pixels, float $dpi = 72): float
    {
        if (0.0 === $dpi) {
            return $pixels;
        }

        return (float) $pixels * 25.4 / $dpi;
    }

    /**
     * Converts the given pixels to the user unit using 72 dots per each (DPI).
     *
     * @param float|int $pixels the pixels to convert
     *
     * @return float the converted value as user unit
     *
     * @see PdfDocument::pixels2mm()
     * @see PdfDocument::points2UserUnit()
     */
    public function pixels2UserUnit(float|int $pixels): float
    {
        return (float) $pixels * 72.0 / 96.0 / $this->scaleFactor;
    }

    /**
     * Converts the given points to user unit.
     *
     * @param float|int $points the points to convert
     *
     * @return float the converted value as user unit
     *
     * @see PdfDocument::pixels2mm()
     * @see PdfDocument::pixels2UserUnit()
     */
    public function points2UserUnit(float|int $points): float
    {
        return (float) $points / $this->scaleFactor;
    }

    /**
     * Outputs a rectangle.
     *
     * It is possible to put a link on the rectangle.
     *
     * @param float                       $x      the abscissa of the upper-left corner
     * @param float                       $y      the ordinate of the upper-left corner
     * @param float                       $width  the width of the rectangle
     * @param float                       $height the height of the rectangle
     * @param PdfRectangleStyle|PdfBorder $style  the style of rendering
     * @param string|int|null             $link   an URL or identifier returned by <code>addLink()</code>
     */
    public function rect(
        float $x,
        float $y,
        float $width,
        float $height,
        PdfRectangleStyle|PdfBorder $style = PdfRectangleStyle::BORDER,
        string|int|null $link = null
    ): static {
        if ($style instanceof PdfRectangleStyle) {
            $scaleFactor = $this->scaleFactor;
            $this->outf(
                '%.2F %.2F %.2F %.2F re %s',
                $x * $scaleFactor,
                ($this->height - $y) * $scaleFactor,
                $width * $scaleFactor,
                -$height * $scaleFactor,
                $style->value
            );
        } elseif ($style->isAny()) {
            $this->out($this->formatBorders($x, $y, $width, $height, $style));
        }

        if (self::isLink($link)) {
            $this->link($x, $y, $width, $height, $link);
        }

        return $this;
    }

    /**
     * Outputs a rectangle.
     *
     * It is possible to put a link on the rectangle.
     *
     * @param PdfRectangle                $rectangle the rectangle to draw
     * @param PdfRectangleStyle|PdfBorder $style     the style of rendering
     * @param string|int|null             $link      an URL or identifier returned by <code>addLink()</code>
     */
    public function rectangle(
        PdfRectangle $rectangle,
        PdfRectangleStyle|PdfBorder $style = PdfRectangleStyle::BORDER,
        string|int|null $link = null
    ): static {
        return $this->rect(
            $rectangle->x,
            $rectangle->y,
            $rectangle->width,
            $rectangle->height,
            $style,
            $link
        );
    }

    /**
     * Defines an alias for the total number of pages.
     *
     * It will be substituted as the document is closed.
     */
    public function setAliasNumberPages(string $aliasNumberPages = '{nb}'): static
    {
        $this->aliasNumberPages = $aliasNumberPages;

        return $this;
    }

    /**
     * Sets a value indicating if the alpha channel is used.
     */
    public function setAlphaChannel(bool $alphaChannel): static
    {
        $this->alphaChannel = $alphaChannel;

        return $this;
    }

    /**
     * Defines the author of the document.
     *
     * @param string $author the name of the author
     * @param bool   $isUTF8 indicates if the string is encoded in ISO-8859-1 (<code>false</code>) or
     *                       UTF-8 (<code>true</code>)
     */
    public function setAuthor(string $author, bool $isUTF8 = false): static
    {
        return $this->addMetaData('Author', $author, $isUTF8);
    }

    /**
     * Enables or disables the automatic page breaking mode.
     *
     * When enabling, the second parameter is the distance from the bottom of the page that defines the triggering
     * limit.
     *
     * @see PdfDocument::isAutoPageBreak()
     */
    public function setAutoPageBreak(bool $autoPageBreak, float $bottomMargin = 0): static
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
    public function setCellMargin(float $margin): static
    {
        $this->cellMargin = \max(0.0, $margin);

        return $this;
    }

    /**
     * Activates or deactivates page compression.
     *
     * When activated, the internal representation of each page is compressed, which leads to a compression ratio of
     * about 2 for the resulting document. Compression is on by default.
     */
    public function setCompression(bool $compression): static
    {
        $this->compression = $compression;

        return $this;
    }

    /**
     * Defines the creator of the document.
     *
     * This is typically the name of the application that generates the PDF.
     *
     * @param string $creator the name of the creator
     * @param bool   $isUTF8  indicates if the string is encoded in ISO-8859-1 (<code>false</code>)
     *                        or UTF-8 (<code>true</code>)
     */
    public function setCreator(string $creator, bool $isUTF8 = false): static
    {
        return $this->addMetaData('Creator', $creator, $isUTF8);
    }

    /**
     * Defines the color used for all drawing operations (lines, rectangles and cell borders).
     *
     * It can be expressed in RGB components or gray scale. The method can be called before the first page is created
     * and the value is retained from page to page.
     *
     * @see PdfDocument::setFillColor()
     * @see PdfDocument::setTextColor()
     */
    public function setDrawColor(?PdfColorInterface $drawColor): static
    {
        $this->drawColor = $drawColor;
        if ($this->page > 0) {
            $this->outColor($this->drawColor, true);
        }

        return $this;
    }

    /**
     * Defines the color used for all filling operations (filled rectangles and cell backgrounds).
     *
     * It can be expressed in RGB components or gray scale. The method can be called before the first page is created
     * and the value is retained from page to page.
     *
     * @see PdfDocument::setDrawColor()
     * @see PdfDocument::setTextColor()
     */
    public function setFillColor(?PdfColorInterface $fillColor): static
    {
        $this->fillColor = $fillColor;
        $this->colorFlag = !$this->computeColorFlag();
        if ($this->page > 0) {
            $this->outColor($this->fillColor, false);
        }

        return $this;
    }

    /**
     * Sets the font used to print character strings.
     *
     * It is mandatory to call this method at least once before printing any text.
     *
     * The font can be either standard or a font added by the <code>addFont()</code> method.
     * Standard fonts use the Windows encoding cp1252 (Western Europe).
     *
     * The method can be called before the first page is created, and the font is kept from page to page.
     *
     * If you just wish to change the current font size, it is simpler to call <code>setFontSizeInPoint()</code>.
     *
     * @param PdfFontName|string|null $family          The font family. It can be either a font name enumeration, a name
     *                                                 defined by <code>addFont()</code> or one of the standard families
     *                                                 (case-insensitive):
     *                                                 <ul>
     *                                                 <li><code>'Courier'</code>: Fixed-width.</li>
     *                                                 <li><code>'Helvetica'</code> or <code>Arial</code>: Synonymous:
     *                                                 sans serif.</li>
     *                                                 <li><code>'Symbol'</code>: Symbolic.</li>
     *                                                 <li><code>'ZapfDingbats'</code>: Symbolic.</li>
     *                                                 </ul>
     *                                                 It is also possible to pass <code>null</code>. In that case, the
     *                                                 current family is kept.
     * @param ?PdfFontStyle           $style           The font style or <code>null</code> to use the current style.
     *                                                 If no style has been specified since the beginning of the
     *                                                 document, the value is <code>PdfFontStyle::REGULAR</code>.
     * @param ?float                  $fontSizeInPoint The font size in points or <code>null</code> to use the current
     *                                                 size. If no size has been specified since the beginning of the
     *                                                 document, the value is 9.0.
     *
     * @throws PdfException if the font is undefined
     *
     * @see PdfDocument::addFont()
     * @see PdfDocument::setFontSizeInPoint()
     */
    public function setFont(
        PdfFontName|string|null $family = null,
        ?PdfFontStyle $style = null,
        ?float $fontSizeInPoint = null
    ): static {
        if ($family instanceof PdfFontName) {
            $family = $family->value;
        }
        $family = null === $family ? $this->fontFamily : \strtolower($family);
        $style ??= $this->fontStyle;
        $fontSizeInPoint ??= $this->fontSizeInPoint;
        $this->underline = $style->isUnderLine();
        // test if the font is already selected
        if ($this->fontFamily === $family && $this->fontStyle === $style && $this->fontSizeInPoint === $fontSizeInPoint) {
            return $this;
        }
        // remove underline style
        if ($this->underline) {
            $style = $style->removeUnderLine();
        }
        // test if the font is already loaded
        $fontKey = $family . $style->value;
        if (!isset($this->fonts[$fontKey])) {
            // test if one of the core fonts
            if ('arial' === $family) {
                $family = 'helvetica';
            }
            $name = PdfFontName::tryFromFamily($family);
            if (!$name instanceof PdfFontName) {
                throw PdfException::format('Undefined font: %s %s.', $family, $style->value);
            }
            if (PdfFontName::SYMBOL === $name || PdfFontName::ZAPFDINGBATS === $name) {
                $style = PdfFontStyle::REGULAR;
            }
            $fontKey = $family . $style->value;
            if (!isset($this->fonts[$fontKey])) {
                $this->addFont($family, $style);
            }
        }
        // select it
        $this->fontFamily = $family;
        $this->fontStyle = $style;
        $this->fontSizeInPoint = $fontSizeInPoint;
        $this->fontSize = $fontSizeInPoint / $this->scaleFactor;
        $this->currentFont = $this->fonts[$fontKey];
        $this->outCurrentFont();

        return $this;
    }

    /**
     * Defines the size, in user unit, of the current font.
     */
    public function setFontSize(float $fontSize): static
    {
        return $this->setFontSizeInPoint($fontSize * $this->scaleFactor);
    }

    /**
     * Defines the size, in points, of the current font.
     *
     * @see PdfDocument::setFont()
     */
    public function setFontSizeInPoint(float $fonsSizeInPoint): static
    {
        if ($this->fontSizeInPoint === $fonsSizeInPoint) {
            return $this;
        }
        $this->fontSizeInPoint = $fonsSizeInPoint;
        $this->fontSize = $fonsSizeInPoint / $this->scaleFactor;
        $this->outCurrentFont();

        return $this;
    }

    /**
     * Associates keywords with the document.
     *
     * @param string $keywords The list of keywords separated by space
     * @param bool   $isUTF8   Indicates if the string is encoded in ISO-8859-1 (<code>false</code>)
     *                         or UTF-8 (<code>true</code>)
     */
    public function setKeywords(string $keywords, bool $isUTF8 = false): static
    {
        return $this->addMetaData('Keywords', $keywords, $isUTF8);
    }

    /**
     * Sets the display layout.
     */
    public function setLayout(PdfLayout $layout): static
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Defines the left margin.
     *
     * The method can be called before creating the first page.
     */
    public function setLeftMargin(float $leftMargin): static
    {
        $this->leftMargin = $leftMargin;
        if ($this->page > 0 && $this->x < $leftMargin) {
            $this->x = $leftMargin;
        }

        return $this;
    }

    /**
     * Sets the line cap.
     *
     * The method can be called before the first page is created and the value is retained from page to page.
     */
    public function setLineCap(PdfLineCap $lineCap): static
    {
        if ($this->lineCap !== $lineCap) {
            $this->lineCap = $lineCap;
            $this->outLineCap();
        }

        return $this;
    }

    /**
     * Sets the line join style.
     *
     * The method can be called before the first page is created and the value is retained from page to page.
     */
    public function setLineJoin(PdfLineJoin $lineJoin): static
    {
        if ($this->lineJoin !== $lineJoin) {
            $this->lineJoin = $lineJoin;
            $this->outLineJoin();
        }

        return $this;
    }

    /**
     * Defines the line width.
     *
     * By default, the value equals 0.2 mm. The method can be called before the first page is created and the value is
     * retained from page to page.
     */
    public function setLineWidth(float $lineWidth): static
    {
        $this->lineWidth = $lineWidth;
        $this->outLineWidth();

        return $this;
    }

    /**
     * Defines the page and position a link points to.
     *
     * @param int        $link the link identifier returned by <code>addLink()</code>
     * @param float|null $y    the ordinate of the target position. A <code>null</code> value indicates the current
     *                         ordinate.
     *                         The default value is 0 (top of the page).
     * @param int|null   $page the target page or <code>null</code> to use the current page
     *
     * @see PdfDocument::addLink()
     * @see PdfDocument::link()
     * @see PdfDocument::createLink()
     */
    public function setLink(int $link, ?float $y = 0, ?int $page = null): static
    {
        $y ??= $this->y;
        $page ??= $this->page;
        $this->links[$link] = [$page, $y];

        return $this;
    }

    /**
     * Defines the left, the top and the right margins.
     *
     * By default, they are equal to 1 cm. Call this method to change them.
     *
     * @param float  $leftMargin  the left margin
     * @param float  $topMargin   the top margin
     * @param ?float $rightMargin the right margin or <code>null</code> to use the left margin
     */
    public function setMargins(float $leftMargin, float $topMargin, ?float $rightMargin = null): static
    {
        $this->leftMargin = $leftMargin;
        $this->topMargin = $topMargin;
        $this->rightMargin = $rightMargin ?? $leftMargin;

        return $this;
    }

    /**
     * Define how the document shall be displayed when opened.
     */
    public function setPageMode(PdfPageMode $pageMode): static
    {
        $this->pageMode = $pageMode;

        return $this;
    }

    /**
     * Defines the abscissa and ordinate of the current position.
     *
     * If the passed values are negative, they are relative respectively to the right (x) and bottom (y) of the page.
     */
    public function setPosition(PdfPoint $position): static
    {
        return $this->setXY($position->x, $position->y);
    }

    /**
     * Defines the producer of the document.
     *
     * @param string $producer the producer
     * @param bool   $isUTF8   Indicates if the string is encoded in ISO-8859-1 (<code>false</code>)
     *                         or UTF-8 (<code>true</code>)
     */
    public function setProducer(string $producer, bool $isUTF8 = false): static
    {
        return $this->addMetaData('Producer', $producer, $isUTF8);
    }

    /**
     * Defines the right margin.
     *
     * The method can be called before creating the first page.
     */
    public function setRightMargin(float $rightMargin): static
    {
        $this->rightMargin = $rightMargin;

        return $this;
    }

    /**
     * Defines the subject of the document.
     *
     * @param string $subject the subject
     * @param bool   $isUTF8  Indicates if the string is encoded in ISO-8859-1 (<code>false</code>)
     *                        or UTF-8 (<code>true</code>)
     */
    public function setSubject(string $subject, bool $isUTF8 = false): static
    {
        return $this->addMetaData('Subject', $subject, $isUTF8);
    }

    /**
     * Defines the color used for text.
     *
     * It can be expressed in RGB components or gray scale. The method can be called before the first page is created
     * and the value is retained from page to page.
     *
     * @see PdfDocument::setDrawColor()
     * @see PdfDocument::setFillColor()
     */
    public function setTextColor(?PdfColorInterface $textColor): static
    {
        $this->textColor = $textColor;
        $this->colorFlag = !$this->computeColorFlag();

        return $this;
    }

    /**
     * Defines the title of the document.
     *
     * @param string $title  the title
     * @param bool   $isUTF8 Indicates if the string is encoded in ISO-8859-1 (<code>false</code>)
     *                       or UTF-8 (<code>true</code>)
     */
    public function setTitle(string $title, bool $isUTF8 = false): static
    {
        $this->title = $title;

        return $this->addMetaData('Title', $title, $isUTF8);
    }

    /**
     * Defines the top margin.
     *
     * The method can be called before creating the first page.
     */
    public function setTopMargin(float $topMargin): static
    {
        $this->topMargin = $topMargin;

        return $this;
    }

    /**
     * Defines the abscissa of the current position.
     *
     * @param float $x the value of the abscissa. If the passed value is negative, it is relative to the right of
     *                 the page.
     */
    public function setX(float $x): static
    {
        $this->x = $x >= 0 ? $x : $this->width + $x;

        return $this;
    }

    /**
     * Defines the abscissa and ordinate of the current position.
     *
     * If the passed values are negative, they are relative respectively to the right (x) and bottom (y) of the page.
     */
    public function setXY(float $x, float $y): static
    {
        return $this->setX($x)
            ->setY($y, false);
    }

    /**
     * Sets the ordinate and optionally moves the current abscissa back to the left margin.
     *
     * @param float $y      the value of the ordinate. If the value is negative, it is relative to the bottom of
     *                      the page.
     * @param bool  $resetX if <code>true</code>, the abscissa is reset to the left margin
     */
    public function setY(float $y, bool $resetX = true): static
    {
        $this->y = $y >= 0 ? $y : $this->height + $y;
        if ($resetX) {
            $this->x = $this->leftMargin;
        }

        return $this;
    }

    /**
     * Sets the zoom.
     *
     * Pages can be displayed entirely on screen, occupy the full width of the window, use real size, be scaled by
     * a specific zooming factor, or use viewer default (configured in the Preferences menu of Adobe Reader).
     *
     * @param PdfZoom|int $zoom The zoom to use. It can be one of the following values:
     *                          <ul>
     *                          <li>A PDF zoom enumeration.</li>
     *                          <li>A positive number indicating the zooming factor to use.</li>
     *                          </ul>
     *
     * @phpstan-param PdfZoom|positive-int $zoom
     */
    public function setZoom(PdfZoom|int $zoom): static
    {
        $this->zoom = $zoom;

        return $this;
    }

    /**
     * Prints a character string.
     *
     * The origin is on the left of the first character, on the baseline.
     * This method allows placing a string precisely on the page, but it is usually easier to use <code>cell()</code>,
     * <code>multiCell()</code> or <code>write()</code> which are the standard methods to print text.
     *
     * Do nothing if the text is empty.
     *
     * @param float  $x    the abscissa of the origin
     * @param float  $y    the ordinate of the origin
     * @param string $text the string to print
     *
     * @throws PdfException if the given text is not empty and no font is set
     *
     * @see PdfDocument::write()
     */
    public function text(float $x, float $y, string $text): static
    {
        if ('' === $text) {
            return $this;
        }
        if (null === $this->currentFont) {
            throw PdfException::instance('No font has been set.');
        }
        $output = \sprintf(
            'BT %.2F %.2F Td (%s) Tj ET',
            $x * $this->scaleFactor,
            ($this->height - $y) * $this->scaleFactor,
            $this->escape($text)
        );
        if ($this->underline) {
            $output .= $this->doUnderline($x, $y, $text);
        }
        if ($this->colorFlag && $this->textColor instanceof PdfColorInterface) {
            $output = \sprintf('q %s %s Q', $this->formatColor($this->textColor, true), $output);
        }
        $this->out($output);

        return $this;
    }

    /**
     * Ensure that this PDF version is equal to or greater than the given version.
     *
     * @param PdfVersion $pdfVersion the minimum version to set
     */
    public function updatePdfVersion(PdfVersion $pdfVersion): static
    {
        if ($this->pdfVersion->isSmaller($pdfVersion)) {
            $this->pdfVersion = $pdfVersion;
        }

        return $this;
    }

    /**
     * Set the cell margins to the given value, call the given user function and reset margins to the previous value.
     *
     * @param callable $callable the function to call
     * @param float    $margin   the cell margin to set. The minimum value allowed is 0.
     */
    public function useCellMargin(callable $callable, float $margin = 0.0): static
    {
        $oldMargin = $this->getCellMargin();
        $this->setCellMargin($margin);
        \call_user_func($callable);
        $this->setCellMargin($oldMargin);

        return $this;
    }

    /**
     * This method prints text from the current position.
     *
     * When the right margin is reached, or the line feed character ("\n") is met, a line break occurs and the text
     * continues from the left margin. Upon method exit, the current position is left just at the end of the text.
     * It is possible to put a link on the text.
     *
     * Do nothing if the text is empty.
     *
     * @param string          $text   the string to print
     * @param float           $height the line height
     * @param string|int|null $link   a URL or an identifier returned by <code>addLink()</code>
     *
     * @throws PdfException if the given text is not empty and no font is set
     *
     * @see PdfDocument::text()
     */
    public function write(string $text, float $height = self::LINE_HEIGHT, string|int|null $link = null): static
    {
        $text = $this->cleanText($text);
        if ('' === $text) {
            return $this;
        }
        if (null === $this->currentFont) {
            throw PdfException::instance('No font has been set.');
        }

        $len = \strlen($text);
        $width = $this->getRemainingWidth();
        $charWidths = $this->currentFont['cw'];
        $widthMax = ($width - 2.0 * $this->cellMargin) * 1000.0 / $this->fontSize;

        $index = 0;
        $newLine = 1;
        $sepIndex = -1;
        $currentIndex = 0;
        $currentWidth = 0.0;
        $border = PdfBorder::none();
        while ($index < $len) {
            $ch = $text[$index];
            if (self::NEW_LINE === $ch) {
                // explicit line break
                $this->cell(
                    $width,
                    $height,
                    \substr($text, $currentIndex, $index - $currentIndex),
                    $border,
                    PdfMove::BELOW,
                    PdfTextAlignment::LEFT,
                    false,
                    $link
                );
                ++$index;
                $sepIndex = -1;
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
            if (' ' === $ch) {
                $sepIndex = $index;
            }
            $currentWidth += (float) $charWidths[$ch];
            if ($currentWidth > $widthMax) {
                // automatic line break
                if (-1 === $sepIndex) {
                    if ($this->x > $this->leftMargin) {
                        // Move to the next line
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
                        $border,
                        PdfMove::BELOW,
                        PdfTextAlignment::LEFT,
                        false,
                        $link
                    );
                } else {
                    $this->cell(
                        $width,
                        $height,
                        \substr($text, $currentIndex, $sepIndex - $currentIndex),
                        $border,
                        PdfMove::BELOW,
                        PdfTextAlignment::LEFT,
                        false,
                        $link
                    );
                    $index = $sepIndex + 1;
                }
                $sepIndex = -1;
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
        // last chunk
        if ($index !== $currentIndex) {
            $this->cell(
                $currentWidth / 1000.0 * $this->fontSize,
                $height,
                \substr($text, $currentIndex),
                $border,
                PdfMove::RIGHT,
                PdfTextAlignment::LEFT,
                false,
                $link
            );
        }

        return $this;
    }

    /**
     * Add the given key/value pair to document meta-datas.
     *
     * @param string $key    the meta-data key
     * @param string $value  the meta-data value
     * @param bool   $isUTF8 indicates if the value is encoded in ISO-8859-1 (<code>false</code>)
     *                       or UTF-8 (<code>true</code>)
     *
     * @phpstan-param non-empty-string $key
     */
    protected function addMetaData(string $key, string $value, bool $isUTF8 = false): static
    {
        $this->metadata[$key] = $isUTF8 ? $value : $this->convertIsoToUtf8($value);

        return $this;
    }

    /**
     * Begin a new page.
     */
    protected function beginPage(
        ?PdfOrientation $orientation = null,
        PdfPageSize|PdfSize|null $size = null,
        ?PdfRotation $rotation = null
    ): void {
        ++$this->page;
        $this->pages[$this->page] = '';
        $this->pageLinks[$this->page] = [];
        $this->state = PdfState::PAGE_STARTED;
        $this->x = $this->leftMargin;
        $this->y = $this->topMargin;
        $this->fontFamily = '';
        // check page size and orientation
        if (!$orientation instanceof PdfOrientation) {
            $orientation = $this->defaultOrientation;
        }
        $size = null === $size ? $this->defaultPageSize : $this->getPageSize($size);
        if ($orientation !== $this->currentOrientation || !$size->equals($this->currentPageSize)) {
            // new size or orientation
            if (PdfOrientation::PORTRAIT === $orientation) {
                $this->width = $size->width;
                $this->height = $size->height;
            } else {
                $this->width = $size->height;
                $this->height = $size->width;
            }
            $this->currentPageSizeInPoint = PdfSize::instance($this->width, $this->height)->scale($this->scaleFactor);
            $this->pageBreakTrigger = $this->height - $this->bottomMargin;
            $this->currentOrientation = $orientation;
            $this->currentPageSize = clone $size;
        }
        if ($orientation !== $this->defaultOrientation || !$size->equals($this->defaultPageSize)) {
            $this->pageInfos[$this->page]['size'] = clone $this->currentPageSizeInPoint;
        }
        if ($rotation instanceof PdfRotation) {
            $this->currentRotation = $rotation;
        }
        if (PdfRotation::DEFAULT !== $this->currentRotation) {
            $this->pageInfos[$this->page]['rotation'] = $this->currentRotation;
        }
    }

    /**
     * Check if the output is not yet started.
     *
     * @throws PdfException if some data has already been output
     */
    protected function checkOutput(): void
    {
        if (\PHP_SAPI !== 'cli' && \headers_sent($file, $line)) {
            throw PdfException::format('Some data has already been output, can not send PDF file (output started at %s:%s).', $file, $line);
        }
        if (false !== \ob_get_length()) {
            // the output buffer is not empty
            $content = \ob_get_contents();
            if (\is_string($content) && 1 === \preg_match('/^(\xEF\xBB\xBF)?\s*$/', $content)) {
                // it contains only a UTF-8 BOM and/or whitespace, let's clean it
                \ob_clean();
            } else {
                throw PdfException::instance('Some data has already been output, can not send PDF file.');
            }
        }
    }

    /**
     * Remove carriage return characters.
     */
    protected function cleanText(string $str): string
    {
        return '' === $str ? $str : \str_replace("\r", '', $str);
    }

    /**
     * Returns if this fill color is equal to this text color.
     */
    protected function computeColorFlag(): bool
    {
        if ($this->fillColor === $this->textColor) {
            return true;
        }

        return $this->fillColor instanceof PdfColorInterface
            && $this->textColor instanceof PdfColorInterface
            && $this->fillColor->equals($this->textColor);
    }

    /**
     * Convert a string from one character encoding to another.
     *
     * @param string          $str           the string to be converted
     * @param string          $to_encoding   the desired encoding of the result
     * @param string[]|string $from_encoding the current encoding used to interpret string
     *
     * @return string the encoded string
     */
    protected function convertEncoding(string $str, string $to_encoding, array|string $from_encoding): string
    {
        /** @phpstan-var string */
        return \mb_convert_encoding($str, $to_encoding, $from_encoding);
    }

    /**
     * Convert the given string from ISO-8859-1 to UTF-8.
     */
    protected function convertIsoToUtf8(string $str): string
    {
        return $this->convertEncoding($str, 'UTF-8', 'ISO-8859-1');
    }

    /**
     * Convert the given string from UTF8 to UTF-16BE.
     */
    protected function convertUtf8ToUtf16(string $str): string
    {
        return "\xFE\xFF" . $this->convertEncoding($str, 'UTF-16BE', 'UTF-8');
    }

    /**
     * Output the underline font.
     *
     * @throws PdfException if no font has been set
     */
    protected function doUnderline(float $x, float $y, string $str): string
    {
        if (null === $this->currentFont) {
            throw PdfException::instance('No font has been set.');
        }

        // underline text
        $up = (float) $this->currentFont['up'];
        $ut = (float) $this->currentFont['ut'];
        $width = $this->getStringWidth($str) + $this->wordSpacing * (float) \substr_count($str, ' ');

        return \sprintf(
            ' %.2F %.2F %.2F %.2F re f',
            $x * $this->scaleFactor,
            ($this->height - ($y - $up / 1000.0 * $this->fontSize)) * $this->scaleFactor,
            $width * $this->scaleFactor,
            -$ut / 1000.0 * $this->fontSizeInPoint
        );
    }

    /**
     * Output the end of this document.
     */
    protected function endDoc(): void
    {
        $this->putHeader();
        $this->putPages();
        $this->putResources();
        // info
        $this->putNewObj();
        $this->put('<<');
        $this->putInfo();
        $this->put('>>');
        $this->putEndObj();
        // catalog
        $this->putNewObj();
        $this->put('<<');
        $this->putCatalog();
        $this->put('>>');
        $this->putEndObj();
        // cross-reference
        $offset = $this->getOffset();
        $this->put('xref');
        $this->putf('0 %d', $this->objectNumber + 1);
        $this->put('0000000000 65535 f ');
        for ($i = 1; $i <= $this->objectNumber; ++$i) {
            $this->putf('%010d 00000 n ', $this->offsets[$i]);
        }
        // trailer
        $this->put('trailer');
        $this->put('<<');
        $this->putTrailer();
        $this->put('>>');
        $this->put('startxref');
        $this->put($offset);
        $this->put('%%EOF');
        $this->state = PdfState::CLOSED;
    }

    /**
     * Output end of the page.
     */
    protected function endPage(): void
    {
        $this->state = PdfState::END_PAGE;
    }

    /**
     * Escape special characters from the given string.
     */
    protected function escape(string $str): string
    {
        return \str_replace(
            ['\\', '(', ')', "\r"],
            ['\\\\', '\\(', '\\)', '\\r'],
            $str
        );
    }

    /**
     * Format the rectangle border output.
     */
    protected function formatBorders(
        float $x,
        float $y,
        float $width,
        float $height,
        PdfBorder $border
    ): string {
        if ($border->isNone()) {
            return '';
        }

        $output = '';
        $left = $x * $this->scaleFactor;
        $top = ($this->height - $y) * $this->scaleFactor;
        $right = ($x + $width) * $this->scaleFactor;
        $bottom = ($this->height - ($y + $height)) * $this->scaleFactor;
        if ($border->isLeft()) {
            $output .= \sprintf('%.2F %.2F m %.2F %.2F l S ', $left, $top, $left, $bottom);
        }
        if ($border->isTop()) {
            $output .= \sprintf('%.2F %.2F m %.2F %.2F l S ', $left, $top, $right, $top);
        }
        if ($border->isRight()) {
            $output .= \sprintf('%.2F %.2F m %.2F %.2F l S ', $right, $top, $right, $bottom);
        }
        if ($border->isBottom()) {
            $output .= \sprintf('%.2F %.2F m %.2F %.2F l S ', $left, $bottom, $right, $bottom);
        }

        return $output;
    }

    /**
     * Gets the formatted color (operation).
     *
     * @param PdfColorInterface $color    the color to format
     * @param bool              $stroking true for stroking operations (draw),
     *                                    false for non-stroking operations (text and fill)
     *
     * @return string the formatted color
     */
    protected function formatColor(PdfColorInterface $color, bool $stroking): string
    {
        return $stroking ? \strtoupper($color->getColor()) : \strtolower($color->getColor());
    }

    /**
     * Gets the image parser for the given type.
     *
     * @param string $type the image type (file extension)
     *
     * @return ?PdfImageParserInterface the image parser, if any; <code>null</code> otherwise
     */
    protected function getImageParser(string $type): ?PdfImageParserInterface
    {
        return match ($type) {
            'jpeg',
            'jpg' => new PdfJpgParser(),
            'gif' => new PdfGifParser(),
            'png' => new PdfPngParser(),
            default => null,
        };
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
     */
    protected function getPageSize(PdfPageSize|PdfSize $size): PdfSize
    {
        if ($size instanceof PdfPageSize) {
            return $size->getSize()->scale(1.0 / $this->scaleFactor);
        }

        if ($size->width > $size->height) {
            return $size->swap();
        }

        return $size;
    }

    /**
     * Send raw HTTP headers.
     *
     * @param string $name   the name of the file
     * @param bool   $isUTF8 indicates if the name is encoded in ISO-8859-1 (<code>false</code>)
     *                       or UTF-8 (<code>true</code>)
     * @param bool   $inline <code>true</code> for inline disposition, <code>false</code> for attachement disposition
     */
    protected function headers(string $name, bool $isUTF8, bool $inline): void
    {
        $disposition = $inline ? 'inline' : 'attachment';
        $encoded = $this->httpEncode('filename', $name, $isUTF8);

        \header('Pragma: public');
        \header('Content-Type: application/pdf');
        \header('Cache-Control: private, max-age=0, must-revalidate');
        \header(\sprintf('Content-Disposition: %s; %s', $disposition, $encoded));
    }

    /**
     * Encode the given name/value pair parameter.
     */
    protected function httpEncode(string $name, string $value, bool $isUTF8): string
    {
        if ($this->isAscii($value)) {
            return \sprintf('%s="%s"', $name, $value);
        }
        if (!$isUTF8) {
            $value = $this->convertIsoToUtf8($value);
        }

        return \sprintf("%s*=UTF-8''%s", $name, \rawurlencode($value));
    }

    /**
     * Returns if the given string is valid for the <code>ASCII</code> encoding.
     *
     * @return bool <code>true</code> if the given string is only containing <code>ASCII</code> characters
     */
    protected function isAscii(string $str): bool
    {
        return \mb_check_encoding($str, 'ASCII');
    }

    /**
     * Returns if the given string is valid for the <code>UTF-8</code> encoding.
     *
     * @return bool <code>true</code> if the given string is only containing <code>UTF-8</code> characters
     */
    protected function isUTF8(string $str): bool
    {
        return \mb_check_encoding($str, 'UTF-8');
    }

    /**
     * Load the font from the given file path.
     *
     * @throws PdfException if the given path does not exist, or the font name is not defined
     *
     * @phpstan-return FontType
     */
    protected function loadFont(string $path): array
    {
        if (!\file_exists($path)) {
            throw PdfException::format('Unable to find the font file: %s.', $path);
        }

        include $path;
        /** @phpstan-var array{name?: string, enc?: string, subsetted?: bool} $font */
        $font = \get_defined_vars();
        if (!isset($font['name'])) {
            throw PdfException::format('No font name defined in file: %s.', $path);
        }

        if (isset($font['enc'])) {
            $font['enc'] = \strtolower($font['enc']);
        }
        $font['subsetted'] ??= false;

        /** @phpstan-var FontType */
        return $font; // @phpstan-ignore varTag.type
    }

    /**
     * Output the given string.
     *
     * @throws PdfException if no page has been added, if the end page has been called or if the document is closed
     */
    protected function out(string $output): void
    {
        switch ($this->state) {
            case PdfState::NO_PAGE:
                throw PdfException::instance('No page has been added yet.');
            case PdfState::END_PAGE:
                throw PdfException::instance('Invalid call (end page).');
            case PdfState::CLOSED:
                throw PdfException::instance('The document is closed.');
            case PdfState::PAGE_STARTED:
                $this->pages[$this->page] .= $output . self::NEW_LINE;
                break;
        }
    }

    /**
     * Output the given color; if not null.
     *
     * @param ?PdfColorInterface $color    the color to output
     * @param bool               $stroking true for stroking operations (draw),
     *                                     false for non-stroking operations (text and fill)
     */
    protected function outColor(?PdfColorInterface $color, bool $stroking): void
    {
        if ($color instanceof PdfColorInterface) {
            $this->out($this->formatColor($color, $stroking));
        }
    }

    /**
     * Output the current font.
     *
     * Do nothing if no page is added or if the current font is <code>null</code>.
     */
    protected function outCurrentFont(): void
    {
        if ($this->page > 0 && null !== $this->currentFont) {
            $this->outf('BT /F%d %.2F Tf ET', $this->currentFont['index'], $this->fontSizeInPoint);
        }
    }

    /**
     * Output a formatted string.
     */
    protected function outf(string $format, string|int|float ...$values): void
    {
        $this->out(\sprintf($format, ...$values));
    }

    /**
     * Output the current line cap.
     *
     * Do nothing if no page is added.
     */
    protected function outLineCap(): void
    {
        if ($this->page > 0) {
            $this->outf('%s J', $this->lineCap->value);
        }
    }

    /**
     * Output the current line join.
     *
     * Do nothing if no page is added.
     */
    protected function outLineJoin(): void
    {
        if ($this->page > 0) {
            $this->outf('%s j', $this->lineJoin->value);
        }
    }

    /**
     * Output the line width.
     *
     * Do nothing if no page is added.
     */
    protected function outLineWidth(): void
    {
        if ($this->page > 0) {
            $this->outf('%.2F w', $this->lineWidth * $this->scaleFactor);
        }
    }

    /**
     * Put the given value to this buffer.
     */
    protected function put(string|int $value): void
    {
        $this->buffer .= \sprintf('%s%s', $value, self::NEW_LINE);
    }

    /**
     * Put the catalog to this buffer.
     */
    protected function putCatalog(): void
    {
        $number = $this->pageInfos[1]['number'];
        $this->put('/Type /Catalog');
        $this->put('/Pages 1 0 R');

        if (\is_int($this->zoom)) {
            $this->putf('/OpenAction [%d 0 R /XYZ null null %.2F]', $number, (float) $this->zoom / 100.0);
        } elseif (!$this->zoom->isDefault()) {
            $this->putf('/OpenAction [%d 0 R /%s]', $number, $this->zoom->value);
        }

        if (!$this->layout->isDefault()) {
            $this->putf('/PageLayout /%s', $this->layout->value);
            $this->updatePdfVersion($this->layout->getVersion());
        }

        if (!$this->pageMode->isDefault()) {
            $this->putf('/PageMode /%s', $this->pageMode->value);
            $this->updatePdfVersion($this->pageMode->getVersion());
        }

        if ($this->viewerPreferences->isChanged()) {
            $this->put($this->viewerPreferences->getOutput());
        }
    }

    /**
     * Put the end object to this buffer.
     *
     * @see PdfDocument::putNewObj()
     */
    protected function putEndObj(): void
    {
        $this->put('endobj');
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
     * @throws PdfException if a font file is not found or if a font type is unsupported
     */
    protected function putFonts(): void
    {
        // embedding font file
        foreach ($this->fontFiles as $file => $info) {
            $this->putNewObj();
            $this->fontFiles[$file]['number'] = $this->objectNumber;
            $content = \file_get_contents($file);
            if (false === $content) {
                throw PdfException::format('Font file not found: %s.', $file);
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
            $this->putEndObj();
        }

        // fonts
        foreach ($this->fonts as $key => $font) {
            // encoding
            if (isset($font['diff'])) {
                $enc = $font['enc'] ?? '';
                if (!isset($this->encodings[$enc])) {
                    $this->putNewObj();
                    $this->putf('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [%s]>>', $font['diff']);
                    $this->putEndObj();
                    $this->encodings[$enc] = $this->objectNumber;
                }
            }
            // ToUnicode CMap
            $mapKey = '';
            $name = $font['name'];
            if (isset($font['uv'])) {
                $mapKey = $font['enc'] ?? $name;
                if (!isset($this->charMaps[$mapKey])) {
                    $map = $this->toUnicodeCmap($font['uv']);
                    $this->putStreamObject($map);
                    $this->charMaps[$mapKey] = $this->objectNumber;
                }
            }
            // font object
            $this->fonts[$key]['number'] = $this->objectNumber + 1;
            if ($font['subsetted']) {
                $name = 'AAAAAA+' . $name;
            }
            $type = $font['type'];
            switch ($type) {
                case 'Core':
                    // core font
                    $this->putNewObj();
                    $this->put('<</Type /Font');
                    $this->putf('/BaseFont /%s', $name);
                    $this->put('/Subtype /Type1');
                    if ('Symbol' !== $name && 'ZapfDingbats' !== $name) {
                        $this->put('/Encoding /WinAnsiEncoding');
                    }
                    if (isset($font['uv'])) {
                        $this->putf('/ToUnicode %d 0 R', $this->charMaps[$mapKey]);
                    }
                    $this->put('>>');
                    $this->putEndObj();
                    break;
                case 'Type1':
                case 'TrueType':
                    // Type1 or TrueType/OpenType font
                    $this->putNewObj();
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
                        $this->putf('/ToUnicode %d 0 R', $this->charMaps[$mapKey]);
                    }
                    $this->put('>>');
                    $this->putEndObj();
                    // widths
                    $this->putNewObj();
                    $charWidths = $font['cw'];
                    $output = \array_reduce(
                        \range(\chr(32), \chr(255)),
                        fn (string $carry, string $ch): string => $carry . \sprintf('%d ', $charWidths[$ch]),
                        ''
                    );
                    $this->putf('[%s]', $output);
                    $this->putEndObj();
                    // descriptor
                    $this->putNewObj();
                    $output = \sprintf('<</Type /FontDescriptor /FontName /%s', $name);
                    foreach ($font['desc'] as $descKey => $descValue) {
                        $output .= \sprintf(' /%s %s', $descKey, $descValue);
                    }
                    if (isset($font['file']) && '' !== $font['file']) {
                        $n = $this->fontFiles[$font['file']]['number'];
                        $output .= \sprintf(' /FontFile%s %d 0 R', 'Type1' === $type ? '' : '2', $n);
                    }
                    $output .= '>>';
                    $this->put($output);
                    $this->putEndObj();
                    break;
                default:
                    // allow for additional types
                    $method = 'put' . \ucfirst(\strtolower($type));
                    if (!\method_exists($this, $method)) {
                        throw PdfException::format('Unsupported font type: %s.', $type);
                    }
                    $this->$method($font); // @phpstan-ignore method.dynamicName
                    break;
            }
        }
    }

    /**
     * Put the header to this buffer.
     */
    protected function putHeader(): void
    {
        $this->updatePdfVersion($this->viewerPreferences->getPdfVersion());
        $this->putf('%%PDF-%s', $this->pdfVersion->value);
    }

    /**
     * Put an image to this buffer.
     *
     * @phpstan-param ImageType $image
     */
    protected function putImage(array &$image): void
    {
        $this->putNewObj();
        $image['number'] = $this->objectNumber;
        $this->put('<</Type /XObject');
        $this->put('/Subtype /Image');
        $this->putf('/Width %d', $image['width']);
        $this->putf('/Height %d', $image['height']);
        if ('Indexed' === $image['color_space']) {
            $this->putf(
                '/ColorSpace [/Indexed /DeviceRGB %d %d 0 R]',
                \intdiv(\strlen($image['palette']), 3) - 1,
                $this->objectNumber + 1
            );
        } else {
            $this->putf('/ColorSpace /%s', $image['color_space']);
            if ('DeviceCMYK' === $image['color_space']) {
                $this->put('/Decode [1 0 1 0 1 0 1 0]');
            }
        }
        $this->putf('/BitsPerComponent %d', $image['bits_per_component']);
        if (isset($image['filter'])) {
            $this->putf('/Filter /%s', $image['filter']);
        }
        if (isset($image['decode_parms'])) {
            $this->putf('/DecodeParms <<%s>>', $image['decode_parms']);
        }
        if (isset($image['transparencies']) && [] !== $image['transparencies']) {
            $transparencies = \array_reduce(
                $image['transparencies'],
                fn (string $carry, int $value): string => $carry . \sprintf('%1$d %1$d ', $value),
                ''
            );
            $this->putf('/Mask [%s]', $transparencies);
        }
        if (isset($image['soft_mask'])) {
            $this->putf('/SMask %d 0 R', $this->objectNumber + 1);
        }
        if (isset($image['data'])) {
            $this->putf('/Length %d>>', \strlen($image['data']));
            $this->putStream($image['data']);
        }
        $this->putEndObj();
        // soft mask
        if (isset($image['soft_mask'])) {
            $decodeParms = \sprintf('/Predictor 15 /Colors 1 /BitsPerComponent 8 /Columns %.2f', $image['width']);
            $soft_image = [
                'index' => 0,
                'number' => 0,
                'width' => $image['width'],
                'height' => $image['height'],
                'color_space' => 'DeviceGray',
                'bits_per_component' => 8,
                'filter' => $image['filter'] ?? '',
                'decode_parms' => $decodeParms,
                'data' => $image['soft_mask'],
                'palette' => '',
            ];
            $this->putImage($soft_image);
        }
        // palette
        if ('Indexed' === $image['color_space']) {
            $this->putStreamObject($image['palette']);
        }
    }

    /**
     * Put images to this buffer.
     */
    protected function putImages(): void
    {
        foreach ($this->images as &$image) {
            $this->putImage($image);
            unset($image['data'], $image['soft_mask']);
        }
    }

    /**
     * Put the creation date and meta-data to this buffer.
     */
    protected function putInfo(): void
    {
        $date = \date('YmdHisO');
        $date = \sprintf("D:%s'%s'", \substr($date, 0, -2), \substr($date, -2));
        $this->addMetaData('CreationDate', $date);
        foreach ($this->metadata as $key => $value) {
            $this->putf('/%s %s', $key, $this->textString($value));
        }
    }

    /**
     * Put links to this buffer.
     */
    protected function putLinks(int $number): void
    {
        foreach ($this->pageLinks[$number] as $pageLink) {
            $this->putNewObj();
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
                $index = $link[0];
                $pageInfo = $this->pageInfos[$index] ?? [];
                if (isset($pageInfo['size'])) {
                    $height = $pageInfo['size']->width;
                } else {
                    $height = (PdfOrientation::PORTRAIT === $this->defaultOrientation)
                        ? $this->defaultPageSize->height * $this->scaleFactor
                        : $this->defaultPageSize->width * $this->scaleFactor;
                }
                $output .= \sprintf(
                    '/Dest [%d 0 R /XYZ 0 %.2F null]>>',
                    $pageInfo['number'] ?? 0,
                    $height - $link[1] * $this->scaleFactor
                );
            }
            $this->put($output);
            $this->putEndObj();
        }
    }

    /**
     * Put a new object to this buffer.
     *
     * @see PdfDocument::putEndObj()
     */
    protected function putNewObj(?int $index = null): void
    {
        $index ??= ++$this->objectNumber;
        $this->offsets[$index] = $this->getOffset();
        $this->putf('%d 0 obj', $index);
    }

    /**
     * Put a page to this buffer.
     */
    protected function putPage(int $number): void
    {
        $this->putNewObj();
        $this->put('<</Type /Page');
        $this->put('/Parent 1 0 R');
        if (isset($this->pageInfos[$number]['size'])) {
            $this->putf(
                '/MediaBox [0 0 %.2F %.2F]',
                $this->pageInfos[$number]['size']->width,
                $this->pageInfos[$number]['size']->height
            );
        }
        if (isset($this->pageInfos[$number]['rotation'])) {
            $this->putf('/Rotate %d', $this->pageInfos[$number]['rotation']->value);
        }
        $this->put('/Resources 2 0 R');
        if ([] !== $this->pageLinks[$number]) {
            $output = '/Annots [';
            foreach ($this->pageLinks[$number] as $pageLink) {
                $output .= \sprintf('%d 0 R ', $pageLink[5]);
            }
            $output .= ']';
            $this->put($output);
        }
        if ($this->alphaChannel) {
            $this->put('/Group <</Type /Group /S /Transparency /CS /DeviceRGB>>');
        }
        $this->putf('/Contents %d 0 R>>', $this->objectNumber + 1);
        $this->putEndObj();
        // page content
        if ('' !== $this->aliasNumberPages) {
            $this->pages[$number] = \str_replace($this->aliasNumberPages, (string) $this->page, $this->pages[$number]);
        }
        $this->putStreamObject($this->pages[$number]);
        // link annotations
        $this->putLinks($number);
    }

    /**
     * Put pages to this buffer.
     */
    protected function putPages(): void
    {
        $page = $this->page;
        $number = $this->objectNumber;
        for ($i = 1; $i <= $page; ++$i) {
            $this->pageInfos[$i]['number'] = ++$number;
            ++$number;
            foreach ($this->pageLinks[$i] as &$pageLink) {
                $pageLink[5] = ++$number;
            }
        }
        for ($i = 1; $i <= $page; ++$i) {
            $this->putPage($i);
        }
        // pages root
        $this->putNewObj(1);
        $this->put('<</Type /Pages');
        $kids = '/Kids [';
        for ($i = 1; $i <= $page; ++$i) {
            $kids .= \sprintf('%d 0 R ', $this->pageInfos[$i]['number']);
        }
        $kids .= ']';
        $this->put($kids);
        $this->putf('/Count %d', $page);
        if (PdfOrientation::PORTRAIT === $this->defaultOrientation) {
            $width = $this->defaultPageSize->width;
            $height = $this->defaultPageSize->height;
        } else {
            $width = $this->defaultPageSize->height;
            $height = $this->defaultPageSize->width;
        }
        $this->putf('/MediaBox [0 0 %.2F %.2F]', $width * $this->scaleFactor, $height * $this->scaleFactor);
        $this->put('>>');
        $this->putEndObj();
    }

    /**
     * Put the resource dictionary to this buffer.
     */
    protected function putResourceDictionary(): void
    {
        // fonts
        $this->put('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
        $this->put('/Font <<');
        foreach ($this->fonts as $font) {
            $this->putf('/F%d %d 0 R', $font['index'], $font['number']);
        }
        $this->put('>>');

        // images
        $this->put('/XObject <<');
        foreach ($this->images as $image) {
            $this->putf('/I%d %d 0 R', $image['index'], $image['number']);
        }
        $this->put('>>');
    }

    /**
     * Put all resource's dictionary to this buffer.
     */
    protected function putResources(): void
    {
        $this->putFonts();
        $this->putImages();
        // resource dictionary
        $this->putNewObj(2);
        $this->put('<<');
        $this->putResourceDictionary();
        $this->put('>>');
        $this->putEndObj();
    }

    /**
     * Put the stream string to this buffer.
     */
    protected function putStream(string $data): void
    {
        $this->put('stream');
        $this->put($data);
        $this->put('endstream');
    }

    /**
     * Put the stream object to this buffer.
     */
    protected function putStreamObject(string $data): void
    {
        $entries = '';
        if ($this->compression) {
            $entries = '/Filter /FlateDecode ';
            $data = (string) \gzcompress($data);
        }
        $entries .= \sprintf('/Length %d', \strlen($data));
        $this->putNewObj();
        $this->putf('<<%s>>', $entries);
        $this->putStream($data);
        $this->putEndObj();
    }

    /**
     * Put the trailer to this buffer.
     */
    protected function putTrailer(): void
    {
        $this->putf('/Size %d', $this->objectNumber + 1);
        $this->putf('/Root %d 0 R', $this->objectNumber);
        $this->putf('/Info %d 0 R', $this->objectNumber - 1);
    }

    /**
     * Convert the given string.
     */
    protected function textString(string $str): string
    {
        if (!$this->isAscii($str)) {
            $str = $this->convertUtf8ToUtf16($str);
        }

        return \sprintf('(%s)', $this->escape($str));
    }

    /**
     * Convert the V type array to the Unicode character map.
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
                $ranges .= \sprintf('<%02X> <%02X> <%04X>%s', $c, $c + $v[1] - 1, $v[0], self::NEW_LINE);
                ++$nbr;
            } else {
                $chars .= \sprintf('<%02X> <%04X>%s', $c, $v, self::NEW_LINE);
                ++$nbc;
            }
        }
        $output = '/CIDInit /ProcSet findresource begin' . self::NEW_LINE;
        $output .= '12 dict begin' . self::NEW_LINE;
        $output .= 'begincmap' . self::NEW_LINE;
        $output .= '/CIDSystemInfo' . self::NEW_LINE;
        $output .= '<</Registry (Adobe)' . self::NEW_LINE;
        $output .= '/Ordering (UCS)' . self::NEW_LINE;
        $output .= '/Supplement 0' . self::NEW_LINE;
        $output .= '>> def' . self::NEW_LINE;
        $output .= '/CMapName /Adobe-Identity-UCS def' . self::NEW_LINE;
        $output .= '/CMapType 2 def' . self::NEW_LINE;
        $output .= '1 begincodespacerange' . self::NEW_LINE;
        $output .= '<00> <FF>' . self::NEW_LINE;
        $output .= 'endcodespacerange' . self::NEW_LINE;
        if ($nbr > 0) {
            $output .= \sprintf('%d beginbfrange', $nbr) . self::NEW_LINE;
            $output .= \sprintf('%sendbfrange', $ranges) . self::NEW_LINE;
        }
        if ($nbc > 0) {
            $output .= \sprintf('%d beginbfchar', $nbc) . self::NEW_LINE;
            $output .= \sprintf('%sendbfchar', $chars) . self::NEW_LINE;
        }
        $output .= 'endcmap' . self::NEW_LINE;
        $output .= 'CMapName currentdict /CMap defineresource pop' . self::NEW_LINE;
        $output .= 'end' . self::NEW_LINE;

        return $output . 'end';
    }

    /**
     * Update and output the word spacing.
     *
     * @param float $value  the word spacing value to set
     * @param bool  $format <code>true</code> to output the formatted word spacing value, even if equal to 0
     */
    protected function updateWordSpacing(float $value = 0.0, bool $format = false): void
    {
        $this->wordSpacing = $value;
        if ($format || 0.0 !== $this->wordSpacing) {
            $this->outf('%.3F Tw', $this->wordSpacing * $this->scaleFactor);
        } else {
            $this->out('0 Tw');
        }
    }
}
