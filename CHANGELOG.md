# Change log

## Unreleased

- Updated test for `PdfTransparencyTrait` trait.

## 2.0.3 - 2024-08-14

- Added `PdfTransparencyTrait` trait and the corresponding tutorial.
- Updated the Bookmarks tutorial with a screenshot.
- Updated name space of `PdfVersionTest` class.
- Update PHP version in `composer.json`.
- Added `PdfVersion` enumeration.
- Updated `addPageIndex()` function signature for `PdfBookmarkTrait` trait.

## 2.0.2 - 2024-08-12

- Added `PdfBookmarkTrait` trait.
- Make `unit` parameter as protected for `PdfDocument`.
- Updated name space of `PdfCompareEscape` class.
- Updated `codecov-action` version to 4.5.0.

## 2.0.1 - 2024-08-09

- Updated `README.md` file.
- Updated PHP documentation and tutorials.

## 2.0.0.0 - 2024-08-08

- Updated FPDF version and producer of `PdfDocument`.
- **BC Break!**: Modified source structure by adding Enums, Interfaces
  and Traits directories.

## 1.9.0.9 - 2024-08-08

- Added `convert()` function to `PdfUnit` enumeration.
- Updated PHP-CS-Fixer configuration.
- Removed the creation date field from `PdfDocument`.
- Reworked internal code of `PdfDocument`.
- Updated the namespace of `PdfEnumDefaultInterfaceTest` class.
- Added `isAny()` function to `PdfBorder` class.

## 1.9.0.8 - 2024-08-02

- Added `draw()` function to `PdfBorder` class.
- Updated PHP doc.
- Added test for `PdfDocument::isLink()` function.
- Reworked `putFonts` function of `PdfDocument` class.
- Added test for `PdfEnumDefaultInterface`.
- Removed covers class attribute.
- Updated to Symfony v7.1.2 and Rector v1.2.0.
- Updated to Symfony v7.1.1.

## 1.9.0.7 - 2024-06-04

- Updated `phpunit/phpunit` to version `^11.0`.
- Updated `vimeo/psalm` to `dev-master` branch.
- Added `convertEncoding` function to `PdfDocument` class.

## 1.9.0.6 - 2024-06-03

- Updated `PdfDocument` class:
  - Replaced return value `self` by `static`.
- Updated `phpstan` ignore doc.

## 1.9.0.5 - 2024-05-31

- Added `PdfViewerPreferences` class and associate enumerations.
- Reworked the `PdfDocument::loadFont()` function.
- Small PHPDoc corrections.

## 1.9.0.4 - 2024-05-30

- Added `previous` parameter to `PdfException::instance()` function.
- Imported `CoversClass` to unit tests.

## 1.9.0.3 - 2024-05-28

- **Breakdown !** Modified `PdfException` class:
  - Split single `instance` function to `instance` and `format` functions.

## 1.9.0.2 - 2024-05-21

- Updated tutorials.
- Updated markdown lint configuration.
- Updated `.gitattributes` to exclude `.github` folder.

## 1.9.0.1 - 2024-05-16

- Updated rector configuration.
- Updated `psalm/plugin-phpunit` plugin version.
- Updated `PhpCsFixer` configuration.
- Updated `.gitattributes` to exclude non-essential files from distribution.

## 1.9.0.0 - 2024-04-29

- Updated tutorials.
- Updated the lint action.
- Updated the used version in the `README.md` file.
- **Breakdown !** Modified the `PdfDocument` class:
  - Updated `cell()` function by replacing default `$width` argument value
    `0.0` with `null`.
  - Updated `multiCell()` function by replacing default `$width` argument value
    `0.0` with `null`.
  - Updated tests accordingly.

## 1.8.4.16 - 2024-04-27

- Add more tests for `cleanText()` function.
- Reworked the `rect()` function.
- Updated PHP documentation.
- Added tutorials.
- Removed `orklah/psalm-*` plugins.
- Added tests.

## 1.8.4.15 - 2024-04-17

- Modified the `PdfDocument` class:
  - The `cleanText()` function no longer removes new line
    characters (`"\n"`) at the end of string.

## 1.8.4.14 - 2024-04-12

- Modified the `PdfDocument` class:
  - Updated `write()` function signature with default height value.

## 1.8.4.13 - 2024-03-28

- Modified the `PdfDocument` class:
  - Removed `non-empty-string` and `positive-int` annotations.

## 1.8.4.12 - 2024-03-28

- Modified the `PdfDocument` class:
  - Make the `isLink()` function as static.

## 1.8.4.11 - 2024-03-22

- Modified the `PdfDocument` class:
  - Replaced empty string by `null` for `$link` parameter.

## 1.8.4.10 - 2024-03-21

- Modified the `PdfDocument` class:
  - Added `$link` parameter to the `rect()` and `rectangle()` functions.

## 1.8.4.9 - 2024-03-20

- Moved `elao/enum` package dependency from `require-dev` to `require`.
- Modified the `PdfDocument` class:
  - Renamed function `updateVersion()` to `updatePdfVersion()`.

## 1.8.4.8 - 2024-03-19

- Added `PdfImageParserInterface` interface.
- Moved parsing images from `PdfDocument` to dedicated classes.
- Updated tests.

## 1.8.4.7 - 2024-03-02

- Modified the `PdfDocument` class:
  - Added `outLineWidth()` function.
  - Added `headers()` function.
  - Added `setFontSize()` function.
- Added tests for `PDfDocument` properties.
- Renamed tests classes.
- Added `instance()` function to `PdfException` class.
- Added `font` directory to tests.
- Split CI actions.

## 1.8.4.6 - 2024-03-01

- Added tests.
- Added unique CI action and removed all other actions.
- Updated cover class attributes for tests.
- Updated `AbstractTestCase` class by removing date creation when comparing
  content.
- Modified the `PdfDocument` class:
  - Updated return value of the `setPosition()` function.
  - Renamed `getFontSize()` function by `getFontSizeInPoint()`.
  - Renamed `setFontSize()` function by `setFontSizeInPoint()`.
  - Added `getLineWidth()` function.

## 1.8.4.5 - 2024-02-29

- Added `elao/enum` dependency.
- Use `EnumCase` attribute for `PdfPageSize` class.
- Modified the `PdfDocument` class:
  - Removed the `getXY()` function.
  - Added the `getPosition()` and `setPosition()` functions.
- Updated license.

## 1.8.4.4 - 2024-02-27

- Updated license.

## 1.8.4.3 - 2024-02-20

- Modified the `PdfDocument` class:
  - Added `updateWordSpacing()` function.
  - Corrected `multiCell()` function to handle correctly borders.
  - Updated `cleanText()` function to trim line feed character only at the end.
  - Updated `parsePngStream()` function.
  - Updated `setLink()` and `createLink()` functions signature.
- Removed `tutorial` directory.
- Removed `doc` directory.

## 1.8.4.2 - 2024-02-19

- Added the `PdfPoint` class.
- Added the `PdfSize` class.
- Added the `PdfRectangle` class.
- Modified the `PdfDocument` class:
  - Updated class constructor by replacing `float[]` parameter by `PdfSize`.
  - Updated `addPage()` function by replacing `float[]` parameter by `PdfSize`.
  - Added `rectangle()` function.
  - Added `linePoints()` function.
  - Replaced width and height in point members by `PdfSize`.
- Modified the `PdfFontStyle` class:
  - Renamed `fromStyle()` to `fromString()` function.

## 1.8.4.1 - 2024-02-18

- Updated `PdfDocument` with default line height for `cell()`
  and `multiCell()` functions.
- Updated list of `PdfPageSize`.
- Added `PdfLineJoin` enumeration.
- Added `PdfLineCap` enumeration.
- Added `PDFBorder` class.
- Added `PDFState` enumeration.
- Updated Rector to version 1.0.

## 1.8.4 - 2024-02-14

- Replaced static by self as return value.
- Added clean text function.
- Corrected put image.
- Corrected the image soft mask.
- Initialize properties. Added left and right margins.
- Replaced self by static.
- Added test for `PdfFontStyle`.
- Removed replace entries in the `composer.json`.

## 1.8.4-alpha-6 - 2024-02-13

- Update `.styleci.yml`.
- Added `CHANGELOG` file.
- Added tests.
- Added isLink function.
- Added new line constant.
- Updated PHP doc.

## 1.8.4-alpha-5 - 2024-02-11

- Updated PHP-Stan to level 9.
- Added `PdfRotation` enumeration.
- Added tests.

## 1.8.4-alpha-4 - 2024-02-08

- Updated font directory for tests.
- Replaced border integer by boolean.

## 1.8.4-alpha-3 - 2024-02-06

- Added tests.
- Minor corrections for output.

## 1.8.4-alpha-2 - 2024-02-05

- Added Rector.
- Added GitHub actions.
- Update `README.md`
- Added badges.
- Configure Style CI.
- Added license.
- Added rectangle style and text alignment.

## 1.8.4-alpha - 2024-02-04

- Initial release.

## 1.86.0 - 2023-06-25

- Added a parameter to AddFont() to specify the directory where to load the
  font definition file.
- Fixed a bug related to the PDF creation date.

## 1.85 - 2022-11-10

- Removed deprecation notices on PHP 8.2.
- Removed notices when passing null values instead of strings.
- The `FPDF_VERSION` constant was replaced by a class constant.
- The creation date of the PDF now includes the timezone.
- The content-type is now always application/pdf, even for downloads.

## 1.84 - 2021-08-28

- Fixed an issue related to annotations.

## 1.83 - 2021-04-18

- Fixed an issue related to annotations.

## 1.82 - 2019-12-07

- Removed a deprecation notice on PHP 7.4.

## 1.81 - 2015-12-20

- Added GetPageWidth() and GetPageHeight().
- Fixed a bug in SetXY().

## 1.8 - 2015-11-29

- PHP 5.1.0 or higher is now required.
- The MakeFont utility now subsets fonts, which can greatly reduce font sizes.
- Added ToUnicode CMaps to improve text extraction.
- Added a parameter to AddPage() to rotate the page.
- Added a parameter to SetY() to indicate whether the x position should be reset
  or not.
- Added a parameter to Output() to specify the encoding of the name, and special
  characters are now properly encoded. Additionally, the order of the first two
  parameters was reversed to be more logical (however the old order is still
  supported for compatibility).
- The Error() method now throws an exception.
- Adding contents before the first AddPage() or after Close() now raises an
  error.
- Outputting text with no font selected now raises an error.

## 1.7 - 2011-06-18

- The MakeFont utility has been completely rewritten and doesn't depend on
  ttf2pt1 anymore.
- Alpha channel is now supported for PNGs.
- When inserting an image, it is now possible to specify its resolution.
- Default resolution for images was increased from 72 to 96 dpi.
- When inserting a GIF image, no temporary file is used anymore if the PHP
  version is 5.1 or higher.
- When output buffering is enabled and the PDF is about to be sent, the buffer
  is now cleared if it contains only a UTF-8 BOM and/or whitespace (instead of
  throwing an error).
- Symbol and ZapfDingbats fonts now support underline style.
- Custom page sizes are now checked to ensure that width is smaller than height.
- Standard font files were changed to use the same format as user fonts.
- A bug in the embedding of Type1 fonts was fixed.
- A bug related to SetDisplayMode() and the current locale was fixed.
- A display issue occurring with the Adobe Reader X plug-in was fixed.
- An issue related to transparency with some versions of Adobe Reader was fixed.
- The Content-Length header was removed because it caused an issue when the
  HTTP server applies compression.

## 1.6 - 2008-08-03

- PHP 4.3.10 or higher is now required.
- GIF image support.
- Images can now trigger page breaks.
- Possibility to have different page formats in a single document.
- Document properties (author, creator, keywords, subject and title) can now be
  specified in UTF-8.
- Fixed a bug: when a PNG was inserted through a URL, an error sometimes
  occurred.
- An automatic page break in Header() doesn't cause an infinite loop anymore.
- Removed some warning messages appearing with recent PHP versions.
- Added HTTP headers to reduce problems with IE.

## 1.53 - 2004-12-31

- When the font subdirectory is in the same directory as fpdf.php, it is no
  longer necessary to define the FPDF_FONTPATH constant.
- The array $HTTP_SERVER_VARS is no longer used. It could cause trouble on
  PHP5-based configurations with the register_long_arrays option disabled.
- Fixed a problem related to Type1 font embedding, which caused trouble to some
  PDF processors.
- The file name sent to the browser could not contain a space character.
- The Cell() method could not print the number 0 (you had to pass the
  string '0').

## 1.52 - 2003-12-30

- Image() now displays the image at 72 dpi if no dimension is given.
- Output() takes a string as second parameter to indicate the destination.
- Open() is now called automatically by AddPage().
- Inserting remote JPEG images doesn't generate an error any longer.
- The decimal separator is forced to dot in the constructor.
- Added several encodings (Turkish, Thai, Hebrew, Ukrainian and Vietnamese).
- The last line of a right-aligned MultiCell() was not correctly aligned if it
  was terminated by a carriage return.
- No more error message about already sent headers when outputting the PDF to
  the standard output from the command line.
- The underlining was going too far for text containing characters \, (or).
- $HTTP_ENV_VARS has been replaced by $HTTP_SERVER_VARS.

## 1.51 - 2002-08-03

- Type1 font support.
- Added Baltic encoding.
- The class now works internally in points with the origin at the bottom order
  to avoid two bugs occurring with Acrobat 5:*. The line thickness was too
  large when printed on Windows 98 SE and ME TrueType fonts
  didn't appear immediately inside the plug-in (a substitution font was used),
  one had to cause a window refresh to make them show up.
- It is no longer necessary to set the decimal separator as dot to produce valid
  documents.
- The clickable area in a cell was always on the left independently of the
  text alignment.
- JPEG images in CMYK mode appeared in inverted colors.
- Transparent PNG images in grayscale or true color mode were incorrectly
  handled.
- Adding new fonts now works correctly even with the magic_quotes_runtime
  option set to on.

## 1.5 - 2002-05-28

- TrueType font (AddFont()) and encoding support (Western and Eastern Europe,
  Cyrillic and Greek).
- Added Write() method.
- Added underlined style.
- Internal and external link support (AddLink(), SetLink(), Link()).
- Added right margin management and methods SetRightMargin(), SetTopMargin().
- Modification of SetDisplayMode() to select page layout.
- The border parameter of MultiCell() now let's choose borders to draw as Cell().
- When a document contains no page, Close() now calls AddPage() instead of
  causing a fatal error.

## 1.41 - 2002-03-13

- Fixed SetDisplayMode() which no longer worked (the PDF viewer used its
  default display).

## 1.4 - 2002-03-02

- PHP3 is no longer supported.
- Page compression (SetCompression()).
- Choice of page format and possibility to change orientation inside document.
- Added AcceptPageBreak() method.
- Ability to print the total number of pages (AliasNbPages()).
- Choice of cell borders to draw.
- New mode for Cell(): the current position can now move under the cell.
- Ability to include an image by specifying height only (width is calculated
  automatically).
- Fixed a bug: when a justified line triggered a page break, the footer
  inherited the corresponding word spacing.

## 1.31 - 2002-01-12

- Fixed a bug in drawing frame with MultiCell(): the last line always started
  from the left margin.
- Removed Expires HTTP header (gives trouble in some situations).
- Added Content-disposition HTTP header (seems to help in some situations).

## 1.3 - 2001-12-03

- Line break and text justification support (MultiCell()).
- Color support (SetDrawColor(), SetFillColor(), SetTextColor()). Possibility
  to draw filled rectangles and paint cell background.
- A cell whose width is declared null extends up to the right margin of the
  page.
- Line width is now retained from page to page and defaults to 0.2 mm.
- Added SetXY() method.
- Fixed a passing by reference done in a deprecated manner for PHP4.

## 1.2 - 2001-11-11

- Added the font metric files and GetStringWidth() method.
- Centering and right-aligning text in cells.
- Display mode control (SetDisplayMode()).
- Added methods to set document properties (SetAuthor(), SetCreator(),
  SetKeywords(), SetSubject(), SetTitle()).
- Possibility to force PDF download by the browser.
- Added SetX() and GetX() methods.
- During automatic page break, current abscissa is now retained.

## 1.11 - 2001-10-20

- PNG support doesn't require PHP4/zlib anymore. Data are now put directly
  into `PDF` without any decompression/recompression stage.
- Image insertion now works correctly even with magic_quotes_runtime option
  set to on.

## 1.1 - 2001-10-07

- JPEG and PNG image support.

## 1.01 - 2001-10-03

- Fixed a bug involving page break: in case when Header() doesn't specify a
  font, the one from previous page was not restored and produced an incorrect
  document.

## 1.0 - 2001-09-17

- First version.
