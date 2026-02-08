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

use fpdf\Enums\PdfAnnotationName;
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
use fpdf\ImageParsers\PdfBmpParser;
use fpdf\ImageParsers\PdfGifParser;
use fpdf\ImageParsers\PdfJpgParser;
use fpdf\ImageParsers\PdfPngParser;
use fpdf\ImageParsers\PdfWebpParser;
use fpdf\Interfaces\PdfColorInterface;
use fpdf\Interfaces\PdfImageParserInterface;
use fpdf\Internal\PdfFont;
use fpdf\Internal\PdfFontFile;
use fpdf\Internal\PdfFontParser;
use fpdf\Internal\PdfImage;
use fpdf\Internal\PdfLink;
use fpdf\Internal\PdfPageAnnotation;
use fpdf\Internal\PdfPageInfo;
use fpdf\Internal\PdfPageLink;

/**
 * Represent a PDF document.
 */
class PdfDocument
{
    /**
     * The default line height in millimeters.
     */
    final public const float LINE_HEIGHT = 5.0;

    /**
     * The FPDF version.
     */
    final public const string VERSION = '4.0';

    // the empty color
    private const string EMPTY_COLOR = '0 G';
    // the true type font type
    private const string FONT_TRUE_TYPE = 'TrueType';
    // the type 1 font type
    private const string FONT_TYPE_1 = 'Type1';
    // the core font type
    private const string FONT_TYPE_CORE = 'Core';
    // the new line separator
    private const string NEW_LINE = "\n";

    /** The alias for the total number of pages. */
    protected string $aliasNumberPages = '{nb}';
    /** Indicates whether the alpha channel is used. */
    protected bool $alphaChannel = false;
    /** The automatic page breaking. */
    protected bool $autoPageBreak = false;
    /** The buffer holding in-memory PDF. */
    protected string $buffer = '';
    /** The cell margin in the user unit. */
    protected float $cellMargin;
    /**
     * The map character codes to character glyphs.
     *
     * @var array<string, int>
     */
    protected array $charMaps = [];
    /** Indicates whether fill and text colors are different. */
    protected bool $colorFlag = false;
    /** The compression flag. */
    protected bool $compression = true;
    /** The current font. */
    protected ?PdfFont $currentFont = null;
    /** The current orientation. */
    protected PdfOrientation $currentOrientation;
    /** The current page size in user unit. */
    protected PdfSize $currentPageSize;
    /** The current page size in point. */
    protected PdfSize $currentPageSizeInPoint;
    /** The current page rotation. */
    protected PdfRotation $currentRotation = PdfRotation::DEFAULT;
    /** The default orientation. */
    protected PdfOrientation $defaultOrientation;
    /** The default page size in the user unit.  */
    protected PdfSize $defaultPageSize;
    /** The commands for drawing color. */
    protected string $drawColor = self::EMPTY_COLOR;
    /**
     * The font encodings.
     *
     * @var array<string, int>
     */
    protected array $encodings = [];
    /** The commands for filling color. */
    protected string $fillColor = self::EMPTY_COLOR;
    /** The current font family. */
    protected string $fontFamily = '';
    /**
     * The font files.
     *
     * @var array<string, PdfFontFile>
     */
    protected array $fontFiles = [];
    /** The directory containing fonts. */
    protected string $fontPath = '';
    /**
     * The used fonts.
     *
     * @var array<string, PdfFont>
     */
    protected array $fonts = [];
    /** The current font size in the user unit. */
    protected float $fontSize;
    /** The current font size in points. */
    protected float $fontSizeInPoint = 9.0;
    /** The current font style. */
    protected PdfFontStyle $fontStyle = PdfFontStyle::REGULAR;
    /**
     * Used images.
     *
     * @var array<string, PdfImage>
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
    /** The line cap. */
    protected PdfLineCap $lineCap;
    /** The line join. */
    protected PdfLineJoin $lineJoin;
    /** The line width in the user unit. */
    protected float $lineWidth;
    /**
     * The internal links.
     *
     * @var array<int, PdfLink>
     */
    protected array $links = [];
    /** The margins in user unit. */
    protected PdfMargins $margins;
    /**
     * The document properties.
     *
     * @var array<string, string>
     */
    protected array $metadata = [];
    /** The current object number. */
    protected int $objectNumber = 2;
    /**
     * The object offsets.
     *
     * @var array<int, int>
     */
    protected array $offsets = [];
    /** The current page number. */
    protected int $page = 0;
    /**
     * The annotations in pages.
     *
     * @var array<int, PdfPageAnnotation[]>
     */
    protected array $pageAnnotations = [];
    /** The threshold used to trigger page breaks. */
    protected float $pageBreakTrigger = 0.0;
    /**
     * The page-related data.
     *
     * @var array<int, PdfPageInfo>
     */
    protected array $pageInfos = [];
    /**
     * The links in pages.
     *
     * @var array<int, PdfPageLink[]>
     */
    protected array $pageLinks = [];
    /** The displayed page mode */
    protected PdfPageMode $pageMode;
    /**
     * The pages.
     *
     * @var array<int, string>
     */
    protected array $pages = [];
    /** The page size in user unit.  */
    protected PdfSize $pageSize;
    /** The PDF version number. */
    protected PdfVersion $pdfVersion;
    /** The scale factor (number of points in the user unit). */
    protected float $scaleFactor;
    /** The current document state. */
    protected PdfState $state = PdfState::NO_PAGE;
    /** The commands for text color. */
    protected string $textColor = self::EMPTY_COLOR;
    /** The document title. */
    protected string $title = '';
    /** The underlining flag. */
    protected bool $underline = false;
    /** The viewer preferences */
    protected PdfViewerPreferences $viewerPreferences;
    /** The word spacing. */
    protected float $wordSpacing = 0.0;
    /** The current x position in the user unit. */
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
        $this->pdfVersion = PdfVersion::getDefault();
        $this->zoom = PdfZoom::getDefault();

        // scale factor
        $this->scaleFactor = $unit->getScaleFactor();
        // font path
        $this->fontPath = $this->getFontPath();
        // font size
        $this->fontSize = $this->divide($this->fontSizeInPoint);
        // page orientation
        $this->defaultOrientation = $orientation;
        $this->currentOrientation = $orientation;
        // page size
        $size = $this->parsePageSize($size);
        $this->defaultPageSize = clone $size;
        $this->currentPageSize = clone $size;
        $this->pageSize = PdfOrientation::PORTRAIT === $orientation ? clone $size : $size->swap();
        $this->currentPageSizeInPoint = $this->pageSize->scale($this->scaleFactor);
        // page margins (10 mm, except bottom = 20 mm)
        $margin = $this->divide(28.35);
        $this->margins = PdfMargins::instance($margin, $margin, $margin);
        // interior cell margin (1 mm)
        $this->cellMargin = $margin / 10.0;
        // line width (0.2 mm)
        $this->lineWidth = $this->divide(0.567);
        // automatic page break
        $this->setAutoPageBreak(true, 2.0 * $margin);
        // producer
        $this->setProducer('FPDF2 ' . self::VERSION);
        // preferences
        $this->viewerPreferences = new PdfViewerPreferences();
    }

    /**
     * Imports a TrueType, an OpenType, or a Type1 font and makes it available.
     *
     * It is necessary to generate a JSON font definition file first with the <code>MakeFont</code> utility.
     *
     * The definition file (and the font file itself in case of embedding) must be present in:
     * <ul>
     * <li>The directory indicated by the 4th parameter (if that parameter is set).</li>
     * <li>The directory indicated by the <code>FPDF_FONTPATH</code> constant (if that constant is defined).</li>
     * <li>The <code>font</code> directory located in the same directory as this class.</li>
     * </ul>
     *
     * @param PdfFontName|string $family The font family. The name can be chosen arbitrarily. If it is a standard
     *                                   family name, it will override the corresponding font.
     * @param PdfFontStyle       $style  The font style. The default value is <code>PdfFontStyle::REGULAR</code>.
     * @param ?string            $file   The name of the font definition file. By default, it is built from the family
     *                                   and style, in the lower case with no space.
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
        $file ??= \str_replace(' ', '', $family) . $style->value . '.json';
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
        $parser = new PdfFontParser();
        $font = $parser->parse($dir . $file);
        $font->index = \count($this->fonts) + 1;
        if ($font->isFile()) {
            // Embedded font
            $key = $dir . $font->file;
            $font->file = $key;
            if (self::FONT_TRUE_TYPE === $font->type) {
                $this->fontFiles[$key] = new PdfFontFile($font->originalsize);
            } else {
                $this->fontFiles[$key] = new PdfFontFile($font->size1, $font->size2);
            }
        }
        $this->fonts[$fontKey] = $font;

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
        $this->links[$index] = new PdfLink();

        return $index;
    }

    /**
     * Adds a new page to the document.
     *
     * If a page is already present, the <code>footer()</code> method is called first to output the footer. Then the
     * page is added, the current position set to the top-left corner according to the left margin and top margin and
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
        $this->outLineJoin();

        // restore context
        $this->lineWidth = $lineWidth;
        $this->outLineWidth();
        if ('' !== $fontFamily) {
            $this->setFont($fontFamily, $fontStyle, $fontSizeInPoint);
        }
        $this->drawColor = $drawColor;
        $this->outDrawColor();
        $this->fillColor = $fillColor;
        $this->outFillColor();
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
            $this->outDrawColor(true);
        }
        if ($this->fillColor !== $fillColor) {
            $this->fillColor = $fillColor;
            $this->outFillColor(true);
        }
        $this->textColor = $textColor;
        $this->colorFlag = $colorFlag;

        return $this;
    }

    /**
     * Add an annotation to the current page.
     *
     * @param string             $text   the annotation text
     * @param ?float             $x      the abscissa of the upper-left corner or <code>null</code> to use the current abscissa
     * @param ?float             $y      the ordinate of the upper-left corner or <code>null</code> to use the current ordinate
     * @param ?float             $width  the width of the annotation or <code>null</code>, to compute the text width
     * @param ?float             $height the height of the annotation or <code>null</code> to use the default line height
     * @param ?string            $title  the optional annotation title
     * @param PdfAnnotationName  $name   the annotation name (icon)
     * @param ?PdfColorInterface $color  the annotation color or <code>null</code> for default (black)
     */
    public function annotation(
        string $text,
        ?float $x = null,
        ?float $y = null,
        ?float $width = null,
        ?float $height = null,
        ?string $title = null,
        PdfAnnotationName $name = PdfAnnotationName::NOTE,
        ?PdfColorInterface $color = null
    ): static {
        $x ??= $this->x;
        $y ??= $this->y;
        $width ??= $this->getStringWidth($text);
        $height ??= self::LINE_HEIGHT;
        $rect = PdfRectangle::instance(
            $this->scale($x),
            $this->scaleY($y),
            $this->scale($width),
            $this->scale($height)
        );
        $this->pageAnnotations[$this->page][] = new PdfPageAnnotation(
            rect: $rect,
            text: $text,
            title: $title,
            name: $name,
            color: $color
        );

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
     *                                 <code>PdfBorder::none()</code>, no border is drawn.
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
                $this->scale($this->x),
                $this->scaleY($this->y),
                $this->scale($width),
                $this->scale(-$height),
                $style->value
            );
        }
        if ('' === $output && $border->isAny()) {
            $output = $this->formatBorders($this->x, $this->y, $width, $height, $border);
        }
        $text = $this->cleanText($text);
        if ('' !== $text) {
            $this->checkCurrentFont();
            $textWidth = $this->getStringWidth($text);
            $dx = match ($align) {
                PdfTextAlignment::RIGHT => $width - $this->cellMargin - $textWidth,
                PdfTextAlignment::CENTER => ($width - $textWidth) / 2.0,
                default => $this->cellMargin,
            };
            if ($this->colorFlag) {
                $output .= \sprintf('q %s ', $this->textColor);
            }
            $output .= \sprintf(
                'BT %.2F %.2F Td (%s) Tj ET',
                $this->scale($this->x + $dx),
                $this->scaleY($this->y + 0.5 * $height + 0.3 * $this->fontSize),
                $this->escape($text)
            );
            if ($this->underline) {
                $output .= $this->doUnderline(
                    $this->x + $dx,
                    $this->y + 0.5 * $height + 0.3 * $this->fontSize,
                    $text,
                    $textWidth
                );
            }
            if ($this->colorFlag) {
                $output .= ' Q';
            }
            if (self::isLink($link)) {
                $this->link(
                    $this->x + $dx,
                    $this->y + 0.5 * $height - 0.5 * $this->fontSize,
                    $textWidth,
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
                $this->x = $this->margins->left;
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
     * called directly by the application. The implementation is empty, so you have to subclass it and override the
     * method if you want specific processing.
     *
     * @see PdfDocument::header()
     */
    public function footer(): void {}

    /**
     * Gets the alias for the total number of pages.
     *
     * The default value is '{nb}'.
     */
    public function getAliasNumberPages(): string
    {
        return $this->aliasNumberPages;
    }

    /**
     * Gets the bottom margin in the user unit.
     *
     * The default value is 10 mm.
     */
    public function getBottomMargin(): float
    {
        return $this->margins->bottom;
    }

    /**
     * Gets the cell margin in the user unit.
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
     * Gets the left margin in the user unit.
     *
     * The default value is 10 mm.
     */
    public function getLeftMargin(): float
    {
        return $this->margins->left;
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
     * @param ?float  $cellMargin the desired cell margin or <code>null</code> to use the current cell margin
     *
     * @return int the number of lines
     *
     * @throws PdfException if the given text is not empty and no font is set
     */
    public function getLinesCount(?string $text, ?float $width = null, ?float $cellMargin = null): int
    {
        return \count($this->splitText($text, $width, $cellMargin));
    }

    /**
     * Gets the line width in the user unit.
     */
    public function getLineWidth(): float
    {
        return $this->lineWidth;
    }

    /**
     * Gets the margins in user unit.
     *
     * <b>Note:</b> Return a copy (clone) of the actual margins. Modify values do not affect the current margins.
     */
    public function getMargins(): PdfMargins
    {
        return clone $this->margins;
    }

    /**
     * Gets the current page number.
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Gets the current page height in the user unit.
     */
    public function getPageHeight(): float
    {
        return $this->pageSize->height;
    }

    /**
     * Gets how the document shall be displayed when opened.
     */
    public function getPageMode(): PdfPageMode
    {
        return $this->pageMode;
    }

    /**
     * Gets the page size in user unit.
     *
     * <b>Note:</b> Return a copy (clone) of the actual page size. Modify values do not affect the current page size.
     */
    public function getPageSize(): PdfSize
    {
        return clone $this->pageSize;
    }

    /**
     * Gets the current page width in the user unit.
     */
    public function getPageWidth(): float
    {
        return $this->pageSize->width;
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
        return $this->getPageWidth() - $this->margins->left - $this->margins->right;
    }

    /**
     * Gets the remaining printable width in the user unit.
     *
     * @return float the value from the current abscissa (x) to the right margin
     */
    public function getRemainingWidth(): float
    {
        return $this->getPageWidth() - $this->margins->right - $this->x;
    }

    /**
     * Gets the right margin in the user unit.
     *
     * The default value is 10 mm.
     */
    public function getRightMargin(): float
    {
        return $this->margins->right;
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
        if (!$this->currentFont instanceof PdfFont || '' === $str) {
            return 0.0;
        }
        $str = $this->cleanText($str);
        if ('' === $str) {
            return 0.0;
        }

        /** @var int[] $keys */
        $keys = \unpack('C*', $str);
        $charWidths = $this->currentFont->cw;
        $width = \array_sum(\array_map(static fn(int $key): int => $charWidths[$key], $keys));

        return (float) $width * $this->fontSize / 1000.0;
    }

    /**
     * Gets the document title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Gets the top margin in the user unit.
     *
     * The default value is 10 mm.
     */
    public function getTopMargin(): float
    {
        return $this->margins->top;
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
     * application. The implementation is empty, so you have to subclass it and override the method if you want
     * specific processing.
     *
     * @see PdfDocument::footer()
     */
    public function header(): void {}

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
     * Supported formats are JPEG, PNG and GIF. The GD extension is required for the GIF format.
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
     *                                moved to the bottom of the image.
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
        // get or parse image
        $image = $this->images[$file] ?? $this->parseImage($file, $type);

        // scale image
        [$width, $height] = $this->scaleImage($image, $width, $height);

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
            $this->scale($width),
            $this->scale($height),
            $this->scale($x),
            $this->scaleY($y + $height),
            $image->index
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
            $this->scale($x1),
            $this->scaleY($y1),
            $this->scale($x2),
            $this->scaleY($y2)
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
        $this->x = $this->margins->left;
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
     * Puts a link on a rectangular area of the current page.
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
        $rect = PdfRectangle::instance(
            $this->scale($x),
            $this->scaleY($y),
            $this->scale($width),
            $this->scale($height)
        );
        $this->pageLinks[$this->page][] = new PdfPageLink($rect, $link);

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
        $entries = $this->splitText($text, $width);
        if ([] === $entries) {
            return $this;
        }

        $border ??= PdfBorder::none();
        if ($border->isAll()) {
            $border1 = PdfBorder::notBottom();
            $border2 = PdfBorder::leftRight();
        } else {
            $border1 = PdfBorder::instance($border->left, $border->top, $border->right, false);
            $border2 = PdfBorder::instance($border->left, false, $border->right, false);
        }

        $firstKey = \array_key_first($entries);
        $lastKey = \array_key_last($entries);
        $width ??= $this->getRemainingWidth();
        $widthMax = $width - 2.0 * $this->cellMargin;

        foreach ($entries as $key => $entry) {
            $line = $entry[0];
            // last line?
            if ($lastKey === $key) {
                $border1->bottom = $border->bottom;
                if ($this->wordSpacing > 0) {
                    $this->updateWordSpacing();
                }
            } elseif (!$entry[1] && PdfTextAlignment::JUSTIFIED === $align) {
                $separators = \substr_count($line, ' ');
                if ($separators > 0) {
                    $textWidth = $this->getStringWidth($line);
                    $wordSpacing = ($widthMax - $textWidth) / (float) $separators;
                    if ($wordSpacing > 0.0) {
                        $this->updateWordSpacing($wordSpacing, true);
                    }
                }
            } elseif ($entry[1] && $this->wordSpacing > 0.0) {
                $this->updateWordSpacing();
            }

            $this->cell($width, $height, $line, $border1, PdfMove::BELOW, $align, $fill);

            // first line?
            if ($firstKey === $key) {
                $border1 = $border2;
            }
        }

        return $this;
    }

    /**
     * Send the document to a given destination: browser, file or string.
     *
     * For a browser, the PDF viewer may be used or a download may be forced.
     * The method first calls <code>close()</code> if necessary to terminate the document.
     *
     * @param PdfDestination $destination The destination where to send the document
     * @param ?string        $name        The name of the file. It is ignored when the destination is
     *                                    <code>PdfDestination::STRING</code>.
     *                                    The default value is 'doc.pdf'.
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
            case PdfDestination::STRING:
                return $this->buffer;
            case PdfDestination::INLINE:
                $this->checkOutput();
                $this->headers($name, $isUTF8, true);
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
        }

        return '';
    }

    /**
     * Converts the given pixels to millimeters using the given dot per each (DPI).
     *
     * If the dot per inch is equal to 0, return the given pixels value.
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
        return $this->divide((float) $pixels * 72.0 / 96.0);
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
        return $this->divide((float) $points);
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
            $this->outf(
                '%.2F %.2F %.2F %.2F re %s',
                $this->scale($x),
                $this->scaleY($y),
                $this->scale($width),
                $this->scale(-$height),
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
        $this->margins->bottom = $bottomMargin;
        $this->pageBreakTrigger = $this->getPageHeight() - $bottomMargin;

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
     * The method can be called before the first page is created, and the value is retained from page to page.
     *
     * @see PdfDocument::setFillColor()
     * @see PdfDocument::setTextColor()
     */
    public function setDrawColor(PdfColorInterface $color): static
    {
        $this->drawColor = \strtoupper($color->getOutput());
        $this->outDrawColor(true);

        return $this;
    }

    /**
     * Defines the color used for all filling operations (filled rectangles and cell backgrounds).
     *
     * The method can be called before the first page is created, and the value is retained from page to page.
     *
     * @see PdfDocument::setDrawColor()
     * @see PdfDocument::setTextColor()
     */
    public function setFillColor(PdfColorInterface $color): static
    {
        $this->fillColor = \strtolower($color->getOutput());
        $this->colorFlag = $this->fillColor !== $this->textColor;
        $this->outFillColor(true);

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
                throw PdfException::format('Undefined font: %s.', $family);
            }
            if ($name->useRegular()) {
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
        $this->fontSize = $this->divide($fontSizeInPoint);
        $this->currentFont = $this->fonts[$fontKey];
        $this->outCurrentFont();

        return $this;
    }

    /**
     * Defines the size, in user unit, of the current font.
     */
    public function setFontSize(float $fontSize): static
    {
        return $this->setFontSizeInPoint($this->scale($fontSize));
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
        $this->fontSize = $this->divide($fonsSizeInPoint);
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
        $this->margins->left = $leftMargin;
        if ($this->page > 0 && $this->x < $leftMargin) {
            $this->x = $leftMargin;
        }

        return $this;
    }

    /**
     * Sets the line cap.
     *
     * The method can be called before the first page is created, and the value is retained from page to page.
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
     * The method can be called before the first page is created, and the value is retained from page to page.
     */
    public function setLineJoin(PdfLineJoin $lineJoin): static
    {
        if ($this->lineJoin !== $lineJoin) {
            $this->lineJoin = $lineJoin;
            $this->outLineJoin(true);
        }

        return $this;
    }

    /**
     * Defines the line width.
     *
     * By default, the value equals 0.2 mm. The method can be called before the first page is created, and the value is
     * retained from page to page.
     */
    public function setLineWidth(float $lineWidth): static
    {
        $this->lineWidth = $lineWidth;
        $this->outLineWidth();

        return $this;
    }

    /**
     * Defines the page and position a link pointing to.
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
        $this->links[$link] = new PdfLink($page ?? $this->page, $y ?? $this->y);

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
        $this->margins->left = $leftMargin;
        $this->margins->top = $topMargin;
        $this->margins->right = $rightMargin ?? $leftMargin;

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
        $this->margins->right = $rightMargin;

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
     * The method can be called before the first page is created, and the value is retained from page to page.
     *
     * @see PdfDocument::setDrawColor()
     * @see PdfDocument::setFillColor()
     */
    public function setTextColor(PdfColorInterface $color): static
    {
        $this->textColor = \strtolower($color->getOutput());
        $this->colorFlag = $this->fillColor !== $this->textColor;

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
        $this->margins->top = $topMargin;

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
        $this->x = $x >= 0 ? $x : $this->getPageWidth() + $x;

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
        $this->y = $y >= 0 ? $y : $this->getPageHeight() + $y;
        if ($resetX) {
            $this->x = $this->margins->left;
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
        $this->checkCurrentFont();
        $output = \sprintf(
            'BT %.2F %.2F Td (%s) Tj ET',
            $this->scale($x),
            $this->scaleY($y),
            $this->escape($text)
        );
        if ($this->underline) {
            $output .= $this->doUnderline($x, $y, $text);
        }
        if ($this->colorFlag) {
            $output = \sprintf('q %s %s Q', $this->textColor, $output);
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
     * Set the cell margins to the given value, call the given user function, and reset margins to the previous value.
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
    public function write(string $text, float $height = self::LINE_HEIGHT, int|string|null $link = null): static
    {
        $entries = $this->splitText(text: $text, resetWidth: true);
        if ([] === $entries) {
            return $this;
        }

        $move = PdfMove::BELOW;
        $width = $this->getRemainingWidth();
        $firstKey = \array_key_first($entries);
        $lastKey = \array_key_last($entries);
        $multiline = $firstKey !== $lastKey;

        foreach ($entries as $key => $entry) {
            $line = $entry[0];
            if ($lastKey === $key) {
                $width = $this->getStringWidth($line);
                $move = PdfMove::RIGHT;
            }
            $this->cell(
                width: $width,
                height: $height,
                text: $line,
                move: $move,
                link: $link
            );
            if ($multiline && $firstKey === $key) {
                $this->x = $this->margins->left;
                $width = $this->getPrintableWidth();
            }
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
     *
     * It is automatically called within the <code>addPage()</code> method and should not be called directly by the
     *  application.
     *
     * @param ?PdfOrientation          $orientation the page orientation or <code>null</code> to use the current
     *                                              orientation
     * @param PdfPageSize|PdfSize|null $size        the page size or <code>null</code> to use the current size
     * @param ?PdfRotation             $rotation    the rotation by which to rotate the page or <code>null</code> to
     *                                              use the current rotation
     */
    protected function beginPage(
        ?PdfOrientation $orientation = null,
        PdfPageSize|PdfSize|null $size = null,
        ?PdfRotation $rotation = null
    ): void {
        ++$this->page;
        $this->pages[$this->page] = '';
        $this->pageLinks[$this->page] = [];
        $this->pageAnnotations[$this->page] = [];
        $this->state = PdfState::PAGE_STARTED;
        $this->x = $this->margins->left;
        $this->y = $this->margins->top;
        $this->fontFamily = '';
        // check page size and orientation
        $orientation ??= $this->defaultOrientation;
        $size = null === $size ? $this->defaultPageSize : $this->parsePageSize($size);
        if ($orientation !== $this->currentOrientation || !$size->equals($this->currentPageSize)) {
            // new size or orientation
            $this->pageSize = PdfOrientation::PORTRAIT === $orientation ? clone $size : $size->swap();
            $this->currentPageSizeInPoint = $this->pageSize->scale($this->scaleFactor);
            $this->pageBreakTrigger = $this->getPageHeight() - $this->margins->bottom;
            $this->currentOrientation = $orientation;
            $this->currentPageSize = clone $size;
        }
        if ($rotation instanceof PdfRotation) {
            $this->currentRotation = $rotation;
        }
        $pageInfo = new PdfPageInfo();
        if ($orientation !== $this->defaultOrientation || !$size->equals($this->defaultPageSize)) {
            $pageInfo->size = clone $this->currentPageSizeInPoint;
        }
        if (PdfRotation::DEFAULT !== $this->currentRotation) {
            $pageInfo->rotation = $this->currentRotation;
        }
        $this->pageInfos[$this->page] = $pageInfo;
    }

    /**
     * Check if the current font is set.
     *
     * @throws PdfException if no font is set
     *
     * @phpstan-assert PdfFont $this->currentFont
     */
    protected function checkCurrentFont(): void
    {
        if (!$this->currentFont instanceof PdfFont) {
            throw PdfException::instance('No font is set.');
        }
    }

    /**
     * Check if the output is not yet started.
     *
     * @throws PdfException if some data has already been output
     */
    protected function checkOutput(): void
    {
        if (!$this->isPhpSapiCli() && $this->isHeadersSent($file, $line)) {
            throw PdfException::format('Some data has already been output, can not send PDF file (output started at %s:%d).', $file, $line);
        }

        // output buffer ?
        $length = \ob_get_length();
        if (false === $length || 0 === $length) {
            return;
        }

        // the output buffer is not empty
        $content = \ob_get_contents();
        if (!\is_string($content) || 1 !== \preg_match('/^(\xEF\xBB\xBF)?\s*$/', $content)) {
            throw PdfException::instance('Some data has already been output, can not send PDF file.');
        }

        // it contains only a UTF-8 BOM and/or whitespace, let's clean it
        \ob_clean();
    }

    /**
     * Remove carriage return characters.
     */
    protected function cleanText(string $str): string
    {
        return '' === $str ? $str : \str_replace("\r", '', $str);
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
        /** @var string */
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
     * Divide the given value by this scale factor.
     *
     * @param float $value the value to divide
     *
     * @return float the divided value
     *
     * @see PdfDocument::scale()
     */
    protected function divide(float $value): float
    {
        return $value / $this->scaleFactor;
    }

    /**
     * Output the underline font.
     */
    protected function doUnderline(float $x, float $y, string $str, ?float $textWidth = null): string
    {
        /** @var PdfFont $font */
        $font = $this->currentFont;
        $up = (float) $font->up;
        $ut = (float) $font->ut;
        $textWidth ??= $this->getStringWidth($str);
        $width = $textWidth + $this->wordSpacing * (float) \substr_count($str, ' ');

        return \sprintf(
            ' %.2F %.2F %.2F %.2F re f',
            $this->scale($x),
            $this->scaleY($y - $up / 1000.0 * $this->fontSize),
            $this->scale($width),
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
        $left = $this->scale($x);
        $top = $this->scaleY($y);
        $right = $this->scale($x + $width);
        $bottom = $this->scaleY($y + $height);
        if ($border->left) {
            $output .= \sprintf('%.2F %.2F m %.2F %.2F l S ', $left, $top, $left, $bottom);
        }
        if ($border->top) {
            $output .= \sprintf('%.2F %.2F m %.2F %.2F l S ', $left, $top, $right, $top);
        }
        if ($border->right) {
            $output .= \sprintf('%.2F %.2F m %.2F %.2F l S ', $right, $top, $right, $bottom);
        }
        if ($border->bottom) {
            $output .= \sprintf('%.2F %.2F m %.2F %.2F l S ', $left, $bottom, $right, $bottom);
        }

        return $output;
    }

    /**
     * Format the given timestamp.
     *
     * @param ?int $timestamp the timestamp to format or the current local time if the timestamp is null
     */
    protected function formatDate(?int $timestamp = null): string
    {
        $date = \date('YmdHisO', $timestamp);

        return \sprintf("D:%s'%s'", \substr($date, 0, -2), \substr($date, -2));
    }

    /**
     * Gets the font path.
     *
     * If the 'FPDF_FONTPATH' constant is defined, then use it.
     * If not, the 'font' child directory is used.
     */
    protected function getFontPath(): string
    {
        /** @var string */
        return \defined('FPDF_FONTPATH') ? \constant('FPDF_FONTPATH') : __DIR__ . '/font/';
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
        return match (\strtolower($type)) {
            'jpeg',
            'jpg' => new PdfJpgParser(),
            'gif' => new PdfGifParser(),
            'png' => new PdfPngParser(),
            'webp' => new PdfWebpParser(),
            'bmp' => new PdfBmpParser(),
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
     * Send raw HTTP headers.
     *
     * Do nothing if the inline parameter is <code>true</code> and the <code></code>PHP_SAPI</code> property is 'cli'.
     *
     * @param string $name   the name of the file
     * @param bool   $isUTF8 indicates if the name is encoded in ISO-8859-1 (<code>false</code>)
     *                       or UTF-8 (<code>true</code>)
     * @param bool   $inline <code>true</code> for inline disposition, <code>false</code> for attachement disposition
     */
    protected function headers(string $name, bool $isUTF8, bool $inline): void
    {
        if ($inline && $this->isPhpSapiCli()) {
            return;
        }

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
     * Join the given strings with a new line as the separator and add an extra new line at the end.
     *
     * @param string[] $values
     */
    protected function implode(array $values): string
    {
        return \implode(self::NEW_LINE, $values) . self::NEW_LINE;
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
     * Checks if and where headers have been sent.
     *
     * @param-out string $filename the source file name where output started
     * @param-out int    $line     the line number where output started
     */
    protected function isHeadersSent(?string &$filename = null, ?int &$line = null): bool
    {
        return \headers_sent($filename, $line);
    }

    /**
     * Returns a value indicating if the <code>PHP_SAPI</code> constant is 'cli'.
     */
    protected function isPhpSapiCli(): bool
    {
        return \PHP_SAPI === 'cli';
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
     * Output the given string.
     *
     * @throws PdfException if no page has been added, if the end page has been called or if the document is closed
     */
    protected function out(string $output): void
    {
        switch ($this->state) {
            case PdfState::NO_PAGE:
                throw PdfException::instance('Invalid call: No page added.');
            case PdfState::END_PAGE:
                throw PdfException::instance('Invalid call: End page.');
            case PdfState::CLOSED:
                throw PdfException::instance('Invalid call: Document closed.');
            case PdfState::PAGE_STARTED:
                $this->pages[$this->page] .= $output . self::NEW_LINE;
                break;
        }
    }

    /**
     * Output the current font.
     *
     * Do nothing if no page is added or if the current font is <code>null</code>.
     */
    protected function outCurrentFont(): void
    {
        if ($this->page > 0 && $this->currentFont instanceof PdfFont) {
            $this->outf('BT /F%d %.2F Tf ET', $this->currentFont->index, $this->fontSizeInPoint);
        }
    }

    /**
     * Output the draw color.
     *
     * Do nothing if no page is added.
     *
     * @param bool $force <code>true</code> to output even if the draw color is empty; <code>false</code> otherwise
     */
    protected function outDrawColor(bool $force = false): void
    {
        if ($this->page > 0 && ($force || self::EMPTY_COLOR !== $this->drawColor)) {
            $this->out($this->drawColor);
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
     * Output the fill color.
     *
     * Do nothing if no page is added.
     *
     * @param bool $force <code>true</code> to output even if the fill color is empty; <code>false</code> otherwise
     */
    protected function outFillColor(bool $force = false): void
    {
        if ($this->page > 0 && ($force || self::EMPTY_COLOR !== $this->fillColor)) {
            $this->out($this->fillColor);
        }
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
     *
     * @param bool $force <code>true</code> to output even if the line join is the default value;
     *                    <code>false</code> otherwise
     */
    protected function outLineJoin(bool $force = false): void
    {
        if ($this->page > 0 && ($force || !$this->lineJoin->isDefault())) {
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
            $this->outf('%.2F w', $this->scale($this->lineWidth));
        }
    }

    /**
     * Parse the given image file and add to this list of images.
     *
     * @param string $file the path or the URL of the image
     * @param string $type the image format. If not specified, the type is inferred from the file extension.
     *
     * @return PdfImage the parsed image
     *
     * @throws PdfException if the image cannot be processed
     */
    protected function parseImage(string $file, string $type): PdfImage
    {
        if ('' === $type) {
            $type = \pathinfo($file, \PATHINFO_EXTENSION);
            if ('' === $type) {
                throw PdfException::format('Image file has no extension and no type was specified: %s.', $file);
            }
        }

        $parser = $this->getImageParser($type);
        if (!$parser instanceof PdfImageParserInterface) {
            throw PdfException::format('Unsupported image type: %s.', $type);
        }

        $image = $parser->parse($this, $file);
        $image->index = \count($this->images) + 1;
        $this->images[$file] = $image;

        return $image;
    }

    /**
     * Parse the page size.
     */
    protected function parsePageSize(PdfPageSize|PdfSize $size): PdfSize
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
     * Put the given value to this buffer.
     */
    protected function put(string|int $value): void
    {
        $this->buffer .= \sprintf('%s%s', $value, self::NEW_LINE);
    }

    /**
     * Put annotations to this buffer.
     */
    protected function putAnnotations(int $page): void
    {
        foreach ($this->pageAnnotations[$page] as $pageAnnotation) {
            $this->putNewObj();
            $output = '<<';
            $rect = $pageAnnotation->formatRectangle();
            $output .= \sprintf(
                '/Type /Annot /Subtype /Text /Rect [%s] /Name %s /Contents %s',
                $rect,
                $pageAnnotation->getNameValue(),
                $this->textString($pageAnnotation->text)
            );
            if ($pageAnnotation->isColor()) {
                $output .= \sprintf('/C [%s]', $pageAnnotation->getColorTag());
            }
            if ($pageAnnotation->isTitle()) {
                $output .= \sprintf('/T %s', $this->textString($pageAnnotation->title));
            }
            $output .= '>>';
            $this->put($output);
            $this->putEndObj();
        }
    }

    /**
     * Put the catalog to this buffer.
     */
    protected function putCatalog(): void
    {
        $number = $this->pageInfos[1]->number;
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
            $this->fontFiles[$file]->number = $this->objectNumber;
            $content = (string) \file_get_contents($file);
            $compressed = \str_ends_with($file, '.z');
            $length1 = $info->length1;
            $length2 = $info->length2;
            if (!$compressed && $info->isLength2()) {
                $content = \substr($content, 6, $length1) . \substr($content, 6 + $length1 + 6, $length2);
            }
            $this->putf('<</Length %d', \strlen($content));
            if ($compressed) {
                $this->put('/Filter /FlateDecode');
            }
            $this->putf('/Length1 %d', $length1);
            if ($info->isLength2()) {
                $this->putf('/Length2 %d /Length3 0', $length2 ?? 0);
            }
            $this->put('>>');
            $this->putStream($content);
            $this->putEndObj();
        }

        // fonts
        foreach ($this->fonts as $key => $font) {
            // encoding
            if ($font->isDiff()) {
                $encoding = $font->encoding ?? '';
                if (!isset($this->encodings[$encoding])) {
                    $this->putNewObj();
                    $this->putf('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [%s]>>', $font->diff);
                    $this->putEndObj();
                    $this->encodings[$encoding] = $this->objectNumber;
                }
            }
            // ToUnicode CMap
            $mapKey = '';
            $name = $font->name;
            if ($font->isUv()) {
                $mapKey = $font->encoding ?? $name;
                if (!isset($this->charMaps[$mapKey])) {
                    $map = $this->toUnicodeCmap($font->uv);
                    $this->putStreamObject($map);
                    $this->charMaps[$mapKey] = $this->objectNumber;
                }
            }
            // font object
            $this->fonts[$key]->number = $this->objectNumber + 1;
            if ($font->subsetted) {
                $name = 'AAAAAA+' . $name;
            }
            $type = $font->type;
            switch ($type) {
                case self::FONT_TYPE_CORE:
                    // core font
                    $this->putNewObj();
                    $this->put('<</Type /Font');
                    $this->putf('/BaseFont /%s', $name);
                    $this->put('/Subtype /Type1');
                    if ('Symbol' !== $name && 'ZapfDingbats' !== $name) {
                        $this->put('/Encoding /WinAnsiEncoding');
                    }
                    if ($font->isUv()) {
                        $this->putf('/ToUnicode %d 0 R', $this->charMaps[$mapKey]);
                    }
                    $this->put('>>');
                    $this->putEndObj();
                    break;
                case self::FONT_TYPE_1:
                case self::FONT_TRUE_TYPE:
                    // Type1 or TrueType/OpenType font
                    $this->putNewObj();
                    $this->put('<</Type /Font');
                    $this->putf('/BaseFont /%s', $name);
                    $this->putf('/Subtype /%s', $type);
                    $this->put('/FirstChar 32 /LastChar 255');
                    $this->putf('/Widths %d 0 R', $this->objectNumber + 1);
                    $this->putf('/FontDescriptor %d 0 R', $this->objectNumber + 2);
                    if ($font->isEncoding() && $font->isDiff()) {
                        $this->putf('/Encoding %d 0 R', $this->encodings[$font->encoding]);
                    } else {
                        $this->put('/Encoding /WinAnsiEncoding');
                    }
                    if ($font->isUv()) {
                        $this->putf('/ToUnicode %d 0 R', $this->charMaps[$mapKey]);
                    }
                    $this->put('>>');
                    $this->putEndObj();
                    // widths
                    $this->putNewObj();
                    $this->putf('[%s]', \implode(' ', \array_slice($font->cw, 32)));
                    $this->putEndObj();
                    // descriptor
                    $this->putNewObj();
                    $output = \sprintf('<</Type /FontDescriptor /FontName /%s', $name);
                    foreach ($font->desc as $descKey => $descValue) {
                        if (\is_array($descValue)) { // FontBBox
                            $descValue = \sprintf('[%s]', \implode(' ', $descValue));
                        }
                        $output .= \sprintf(' /%s %s', $descKey, $descValue);
                    }
                    if ($font->isFile()) {
                        $fontFile = self::FONT_TYPE_1 === $type ? '' : '2';
                        $number = $this->fontFiles[$font->file]->number;
                        $output .= \sprintf(' /FontFile%s %d 0 R', $fontFile, $number);
                    }
                    $output .= '>>';
                    $this->put($output);
                    $this->putEndObj();
                    break;
                default:
                    throw PdfException::format('Unsupported font type: %s.', $type);
            }
        }
    }

    /**
     * Put the header to this buffer.
     */
    protected function putHeader(): void
    {
        $this->updatePdfVersion($this->viewerPreferences->getVersion());
        $this->putf('%%PDF-%s', $this->pdfVersion->value);
    }

    /**
     * Put an image to this buffer.
     */
    protected function putImage(PdfImage $image): void
    {
        $this->putNewObj();
        $image->number = $this->objectNumber;
        $this->put('<</Type /XObject');
        $this->put('/Subtype /Image');
        $this->putf('/Width %d', $image->width);
        $this->putf('/Height %d', $image->height);
        if ('Indexed' === $image->colorSpace) {
            $this->putf(
                '/ColorSpace [/Indexed /DeviceRGB %d %d 0 R]',
                \intdiv(\strlen($image->palette), 3) - 1,
                $this->objectNumber + 1
            );
        } else {
            $this->putf('/ColorSpace /%s', $image->colorSpace);
            if ('DeviceCMYK' === $image->colorSpace) {
                $this->put('/Decode [1 0 1 0 1 0 1 0]');
            }
        }
        $this->putf('/BitsPerComponent %d', $image->bitsPerComponent);
        if ($image->isFilter()) {
            $this->putf('/Filter /%s', $image->filter);
        }
        if ($image->isDecodeParms()) {
            $this->putf('/DecodeParms <<%s>>', $image->decodeParms);
        }
        if ($image->isTransparencies()) {
            $transparencies = \array_reduce(
                $image->transparencies,
                static fn(string $carry, int $value): string => $carry . \sprintf('%1$d %1$d ', $value),
                ''
            );
            $this->putf('/Mask [%s]', $transparencies);
        }
        if ($image->isSoftMask()) {
            $this->putf('/SMask %d 0 R', $this->objectNumber + 1);
        }
        $this->putf('/Length %d>>', \strlen($image->data));
        $this->putStream($image->data);
        $this->putEndObj();

        // soft mask
        if ($image->isSoftMask()) {
            $decodeParms = \sprintf('/Predictor 15 /Colors 1 /BitsPerComponent 8 /Columns %.2f', $image->width);
            $softImage = new PdfImage(
                width: $image->width,
                height: $image->height,
                colorSpace: 'DeviceGray',
                bitsPerComponent: 8,
                data: $image->softMask,
                filter: $image->filter ?? '',
                decodeParms: $decodeParms
            );
            $this->putImage($softImage);
        }
        // palette
        if ('Indexed' === $image->colorSpace) {
            $this->putStreamObject($image->palette);
        }
    }

    /**
     * Put images to this buffer.
     */
    protected function putImages(): void
    {
        foreach ($this->images as $image) {
            $this->putImage($image);
            $image->data = '';
            $image->softMask = null;
        }
    }

    /**
     * Put the creation date and meta-data to this buffer.
     */
    protected function putInfo(): void
    {
        $this->addMetaData('CreationDate', $this->formatDate());
        foreach ($this->metadata as $key => $value) {
            $this->putf('/%s %s', $key, $this->textString($value));
        }
    }

    /**
     * Put links to this buffer.
     */
    protected function putLinks(int $page): void
    {
        foreach ($this->pageLinks[$page] as $pageLink) {
            $this->putNewObj();
            $output = '<<';
            $rect = $pageLink->formatRectangle();
            $output .= \sprintf('/Type /Annot /Subtype /Link /Rect [%s] /Border [0 0 0] ', $rect);
            if (\is_string($pageLink->link)) {
                $output .= \sprintf('/A <</S /URI /URI %s>>', $this->textString($pageLink->link));
            } else {
                $link = $this->links[$pageLink->link];
                $pageInfo = $this->pageInfos[$link->page] ?? new PdfPageInfo();
                if ($pageInfo->isSize()) {
                    $height = $pageInfo->size->width;
                } else {
                    $height = (PdfOrientation::PORTRAIT === $this->defaultOrientation)
                        ? $this->scale($this->defaultPageSize->height)
                        : $this->scale($this->defaultPageSize->width);
                }
                $output .= \sprintf(
                    '/Dest [%d 0 R /XYZ 0 %.2F null]',
                    $pageInfo->number,
                    $height - $this->scale($link->y)
                );
            }
            $output .= '>>';
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
    protected function putPage(int $page): void
    {
        $this->putNewObj();
        $this->put('<</Type /Page');
        $this->put('/Parent 1 0 R');
        $pageInfo = $this->pageInfos[$page];
        if ($pageInfo->isSize()) {
            $this->putf(
                '/MediaBox [0 0 %.2F %.2F]',
                $pageInfo->size->width,
                $pageInfo->size->height
            );
        }
        if ($pageInfo->isRotation()) {
            $this->putf('/Rotate %d', $pageInfo->rotation->value);
        }

        $this->put('/Resources 2 0 R');

        $annotation = '';
        foreach ($this->pageLinks[$page] as $pageLink) {
            $annotation .= \sprintf('%d 0 R ', $pageLink->number);
        }
        foreach ($this->pageAnnotations[$page] as $pageAnnotation) {
            $annotation .= \sprintf('%d 0 R ', $pageAnnotation->number);
        }
        if ('' !== $annotation) {
            $this->putf('/Annots [%s]', $annotation);
        }
        if ($this->alphaChannel) {
            $this->put('/Group <</Type /Group /S /Transparency /CS /DeviceRGB>>');
        }
        $this->putf('/Contents %d 0 R>>', $this->objectNumber + 1);
        $this->putEndObj();
        // page content
        if ('' !== $this->aliasNumberPages) {
            $this->pages[$page] = \str_replace($this->aliasNumberPages, (string) $this->page, $this->pages[$page]);
        }
        $this->putStreamObject($this->pages[$page]);
        // links
        $this->putLinks($page);
        // annotations
        $this->putAnnotations($page);
    }

    /**
     * Put pages to this buffer.
     */
    protected function putPages(): void
    {
        $page = $this->page;
        $number = $this->objectNumber;
        for ($i = 1; $i <= $page; ++$i) {
            $this->pageInfos[$i]->number = ++$number;
            ++$number;
            foreach ($this->pageLinks[$i] as $pageLink) {
                $pageLink->number = ++$number;
            }
            foreach ($this->pageAnnotations[$i] as $pageAnnotation) {
                $pageAnnotation->number = ++$number;
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
            $kids .= \sprintf('%d 0 R ', $this->pageInfos[$i]->number);
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
        $this->putf('/MediaBox [0 0 %.2F %.2F]', $this->scale($width), $this->scale($height));
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
            $this->putf('/F%d %d 0 R', $font->index, $font->number);
        }
        $this->put('>>');

        // images
        $this->put('/XObject <<');
        foreach ($this->images as $image) {
            $this->putf('/I%d %d 0 R', $image->index, $image->number);
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
     * Multiply the given value by this scale factor.
     *
     * @param float $value the value to scale
     *
     * @return float the scaled value
     *
     * @see PdfDocument::divide()
     */
    protected function scale(float $value): float
    {
        return $value * $this->scaleFactor;
    }

    /**
     * Scale the given image.
     *
     * @param PdfImage $image  the parsed image
     * @param float    $width  the desired width of the image
     * @param float    $height the desired height of the image
     *
     * @return array{0: float, 1: float} the scaled image
     */
    protected function scaleImage(PdfImage $image, float $width, float $height): array
    {
        if (0.0 === $width && 0.0 === $height) {
            // put image at 96 dpi
            $width = -96.0;
            $height = -96.0;
        }

        $imageWidth = (float) $image->width;
        $imageHeight = (float) $image->height;
        if ($width < 0.0) {
            $width = $this->divide(-$imageWidth * 72.0 / $width);
        }
        if ($height < 0.0) {
            $height = $this->divide(-$imageHeight * 72.0 / $height);
        }
        if (0.0 === $width) {
            $width = $height * $imageWidth / $imageHeight;
        }
        if (0.0 === $height) {
            $height = $width * $imageHeight / $imageWidth;
        }

        return [$width, $height];
    }

    /**
     * Multiply the given ordinate, from the bottom page height, by this scale factor.
     *
     * @param float $y the ordinate value to scale
     *
     * @return float the ordinate scaled value
     */
    protected function scaleY(float $y): float
    {
        return $this->scale($this->getPageHeight() - $y);
    }

    /**
     * Split the given text for the given width.
     *
     * @param ?string    $text       the text to split
     * @param float|null $width      the desired width or <code>null</code> to use the width extends up to the right
     *                               margin
     * @param ?float     $cellMargin the desired cell margin or <code>null</code> to use the current cell margin
     * @param bool       $resetWidth a value indicating if the second line, if any; use all the printable width
     *                               (<code>true</code>) or keep with the desired width (<code>false</code>)
     *
     * @return array<array{0: string, 1: bool}> an array where each entry contains the line text and a flag indicating
     *                                          if the end line is explicit (<code>true</code>) or if caused by text
     *                                          overflow (<code>false</code>).
     *                                          Returns an empty array if the text is <code>null</code> or empty
     *
     * @throws PdfException if the given text is not empty and no font is set
     */
    protected function splitText(
        ?string $text = '',
        ?float $width = null,
        ?float $cellMargin = null,
        bool $resetWidth = false
    ): array {
        if (null === $text || '' === $text) {
            return [];
        }
        $text = $this->cleanText($text);
        $len = \strlen($text);
        if (0 === $len) {
            return [];
        }
        $this->checkCurrentFont();

        $lines = [];
        $cellMargin ??= $this->cellMargin;
        $width ??= $this->getRemainingWidth();
        $charWidths = $this->currentFont->cw;
        $maxWidth = ($width - 2.0 * $cellMargin) * 1000.0 / $this->fontSize;

        $index = 0;
        $lastIndex = 0;
        $sepIndex = -1;
        $currentWidth = 0.0;

        while ($index < $len) {
            $ch = $text[$index];
            if (self::NEW_LINE === $ch) {
                // explicit line break
                $lines[] = [\substr($text, $lastIndex, $index - $lastIndex), true];
                ++$index;
                $sepIndex = -1;
                $lastIndex = $index;
                $currentWidth = 0.0;
                if ($resetWidth) {
                    $maxWidth = ($this->getPrintableWidth() - 2.0 * $cellMargin) * 1000.0 / $this->fontSize;
                }
                continue;
            }
            if (' ' === $ch) {
                $sepIndex = $index;
            }
            $currentWidth += (float) $charWidths[\ord($ch)];
            if ($currentWidth > $maxWidth) {
                // automatic line break
                if (-1 === $sepIndex) {
                    if ($index === $lastIndex) {
                        ++$index;
                    }
                    $lines[] = [\substr($text, $lastIndex, $index - $lastIndex), false];
                } else {
                    $lines[] = [\substr($text, $lastIndex, $sepIndex - $lastIndex), false];
                    $index = $sepIndex + 1;
                }
                $sepIndex = -1;
                $lastIndex = $index;
                $currentWidth = 0.0;
                if ($resetWidth) {
                    $maxWidth = ($this->getPrintableWidth() - 2.0 * $cellMargin) * 1000.0 / $this->fontSize;
                }
            } else {
                ++$index;
            }
        }

        // last chunk
        $lines[] = [\substr($text, $lastIndex), false];

        return $lines;
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
     * @phpstan-param array<int, int|int[]> $uv
     */
    protected function toUnicodeCmap(array $uv): string
    {
        $chars = [];
        $ranges = [];
        foreach ($uv as $c => $v) {
            if (\is_array($v)) {
                $ranges[] = \sprintf('<%02X> <%02X> <%04X>', $c, $c + $v[1] - 1, $v[0]);
            } else {
                $chars[] = \sprintf('<%02X> <%04X>', $c, $v);
            }
        }
        $output = [
            '/CIDInit /ProcSet findresource begin',
            '12 dict begin',
            'begincmap',
            '/CIDSystemInfo',
            '<</Registry (Adobe)',
            '/Ordering (UCS)',
            '/Supplement 0',
            '>> def',
            '/CMapName /Adobe-Identity-UCS def',
            '/CMapType 2 def',
            '1 begincodespacerange',
            '<00> <FF>',
            'endcodespacerange',
        ];
        if ([] !== $ranges) {
            $output[] = \sprintf('%d beginbfrange', \count($ranges));
            $output[] = \sprintf('%sendbfrange', $this->implode($ranges));
        }
        if ([] !== $chars) {
            $output[] = \sprintf('%d beginbfchar', \count($chars));
            $output[] = \sprintf('%sendbfchar', $this->implode($chars));
        }
        $output[] = 'endcmap';
        $output[] = 'CMapName currentdict /CMap defineresource pop';
        $output[] = 'end';

        return $this->implode($output) . 'end';
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
            $this->outf('%.3F Tw', $this->scale($this->wordSpacing));
        } else {
            $this->out('0 Tw');
        }
    }
}
