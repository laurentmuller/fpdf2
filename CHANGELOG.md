# Change log

## Unreleased

- Updated `PdfFont` constructor.
- Updated `StyleCI` configuration.
- Updated `PHP-CS-Fixer` to version 3.88.0 and the configuration.

## 3.2.1 - 2025-09-25

- Updated tests.
- Removed `@phpstan-*` documentation.
- Added classes instead of arrays for `PdfAttachmentTrait`, `PdfBookmarkTrait`
  and `PdfTransparencyTrait` classes.
- Removed commented code. Updated tests.

## 3.2.0 - 2025-09-24

- `PdfDocument`: Added classes instead of arrays.
- `PdfDocument`: Removed custom function to put font.
- Updated tests.
- `PdfDocument`: Added internal font type constants.
- `PdfDocument`: Simplified `putFonts()` function for character widths.

## 3.1.0 - 2025-09-04

- Removed `makefont` directory.
- `PdfDocument`: Use `unpack()` for the `getStringWidth()` function.
- Set arrow functions as static.
- Updated rector configuration.
- Updated Style CI configuration.
- **BC Break!**: Replaced the PHP font definitions by the
  [JSON](https://www.json.org/json-en.html) format.
- Updated color conversions.
- Updated GitHub Actions.

## 3.0.15 - 2025-07-10

- Updated documentation.
- Updated Rector configuration.
- Small updates with Rector version 2.1.0.

## 3.0.14 - 2025-05-19

- `PdfPolygonTrait`: Use `scaleY()` function.
- `PdfRotationTrait`: Use `scaleY()` function.

## 3.0.13 - 2025-05-18

- `PdfTransparencyTrait`: Internal change.
- `PdfEllipseTrait`: Use `scaleY()` function.
- `PdfDocument`: Added `scaleY()` function.
- `PdfSectorTrait`: Use `deg2rad()` function.
- `PdfRotationTrait`: Use `deg2rad()` function.
- `PdfDocument`: Added `$title` parameter to the `annotation()` function.
- `PdfDocument`: Added `$color` parameter to the `annotation()` function.
- `PdfColorInterface`: Added `getTag()` function.
- Updated test for the `annotation()` function.
- Added the documentation for the `annotation()` function.
- `PdfDocument`: Added `annotation()` function.
- Added `PdfAnnotationName` enumeration.
- `PdfDocument`: Renamed internal parameter names.
- Replaced `@psalm` annotations by `@phpstan`.
- `PdfBorderTest`: Added `assertBorderEquals()` function.

## 3.0.12 - 2025-03-30

- `PdfDocument`: Added `parseImage()` and `scaleImage()` functions.
- `PhpCsFixer`: Added Symfony rule sets.
- `composer.json`: Added composer validate script.
- Added php unit plugin to Psalm.
- Added type-coverage to PHP Stan.
- `PdfFontName`: Added `useRegular()` function.
- Renamed `fixture` directory to `Fixture`.
- Updated exception message.
- Removed unused comment.
- Updated license year.
- `PdfDocument`: Added `checkCurrentFont()` function.
- Added message assertions to unit tests.
- Renamed example files.
- Added `PdfPolygonTrait` trait.

## 3.0.11 - 2025-03-23

- `PdfDashTrait`: Added `dashedRect()` and `dashedRectangle()` functions.
- **BC Break!**: `PdfDashTrait`: Renamed the `setDash()` function
  to `setDashPattern()` and the `resetDash()` function to `resetDashPattern()`.

## 3.0.10 - 205-03-23

- Added `PdfDashTrait` trait.
- **BC Break!**: `PdfAttachmentTrait`: Renamed the `$desc` parameter
  to `$description`.
- `PdfPngParser`: Added internal `getColorSpace()` and `getColors()` functions.
- `PdfPngParser`: Added internal `skip()` function, updated `parseStream()`
  and `checkHeader()` functions.
- Reworked tests.
- `PdfPngParser`: Reworked `parseStream()` function.
- Added test for CMYK image.
- `PdfCmykColor`: Added static colors for each component.
- `PdfDocument`: Reworked `output()` function.

## 3.0.9 - 2025-03-11

- `PdfDocument`: Updated `outLineJoin()` function.
- `PdfDocument`: Added `outDrawColor()` and `outFillColor()` functions.

## 3.0.8 - 2025-02-26

- **BC Break!**: Sets `PdfBorder` properties public, removed the corresponding
  getter and setter functions and added more instances creation.

## 3.0.7 - 2025-02-26

- Updated unit tests.
- Simplified `multiCell()` and `write()` functions.
- Reworked `splitText()` function.
- Updated `getLinesCount()` function to the `PdfDocument` class.
- Added `splitText()` function to the `PdfDocument` class.
- Small optimizations of the `PdfDocument` class.

## 3.0.6 - 2025-02-24

- Added tests for font files.
- Added missing override attribute.
- Updated documentation.
- Updated rector configuration.

## 3.0.5 - 2025-02-06

- Removed the `self::fail()` functions to unit tests.
- Added the `getAliasNumberPages()` function to the `PdfDocument` class.
- Minor modifications.

## 3.0.4 - 2025-01-04

- Replaced the `width` and `height` properties of the `PdfDocument`
  by a `PdfSize` instance.
- Replaced all margins properties of the `PdfDocument` by a `PdfMargins` class.
- Renamed private functions of `PdfSectorTrait`.
- Added `scale()` and `divide()` functions to `PdfDocument` class.

## 3.0.3 - 2025-01-30

- Make `PdfRgbColor` class as read-only.

## 3.0.2 - 2025-01-30

- Updated `PdfRgbColor` class with static methods and return types.

## 3.0.1 - 2025-01-30

- Make `PdfRgbColor` class no more read-only.

## 3.0.0 - 2025-01-30

- Updated `PdfRgbColor` class.
- Updated Symfony to version 7.2.3.
- Updated Rector to version 2.0.
- Updated cache configurations.
- Updated psalm to version 6.0.
- Added default title to the `addPageIndex()` function of
  the `PdfBookmarkTrait` trait.
- Added function definitions in examples.
- Updated examples.
- **BC Break!**: Created dedicated colors namespace. Now the `setDrawColor()`,
  `setFillColor()` and `setTextColor()` functions of `PdfDocument` class take
  a `PdfColorInterface` instance.

## 2.0.14 - 2025-01-23

- Added bitmap image parser.
- **BC Break!**: Moved image parsers to dedicated namespace.
- Added Webp image parser.
- Simplified image parsers.
- Added word spacing test.
- Updated tests for image parsers.
- Added `formatDate` function to `PdfDocument` class.
- Removed `composer.lock` from export.

## 2.0.13 - 2025-01-15

- Update `PDfVersion` when attachments are added.
- Updated documentation.
- Added `PdfAttachmentTrait` trait.
- Updated test for `PdfSectorTrait`.
- Updated `PdfViewerPreferences` class.
- Updated documentation.
- Updated Symfony to version 7.2.1.
- Updated Symfony to version 7.2.0.
- Updated Symfony to version 7.1.9.

## 2.0.12 - 2024-11-25

- Updated `php-cs-fixer` to version 3.65.0.
- Updated `PHPunit` continuous integration action.
- Updated `PHPStan` level to 10.
- Split dependencies in dedicated folders using `bamarni/composer-bin-plugin`.
- Added bleeding edge before update `phpstan` to version 2.0.
- Added badges to `README.md`.
- Updated Symfony to version 7.1.7.
- Updated `.gitattributes` file.
- Updated Symfony to version 7.1.6.

## 2.0.11 - 2024-10-21

- Updated signature of `createLink()` and `setLink()` functions of the
  `PdfDocument` class.
  The `$y` parameter can be null and correspond to the current ordinate.
- **BC Break!**: Renamed function `setWithAlpha()` to `setAlphaChannel()` of
  the `PdfDocument` class.

## 2.0.10 - 2024-10-15

- Corrected bug in `PdfBookmarkTrait` (page number not outputted).
- Updated the namespace for tests.

## 2.0.9 - 2024-10-15

- Updated `PdfLineCap`, `PdfLineJoin` and `PdfZoom` enumerations.
- **BC Break!**: Removed the `setDisplayMode()` function in `PdfDocument` class.
  Use the `setLayout()` and `setZoom()` functions instead.

## 2.0.8 - 2024-10-15

- Added `PdfPageMode` enumeration.
- Added more entries in `PdfLayout` enumeration.

## 2.0.7 - 2024-10-14

- Updated `PdfLayout` enumeration.
- Updated PHPUnit schema version.
- Updated `PdfSectorTrait` trait and tests.
- Updated PHPStan documentation.
- Spelling correction in documentation.
- Updated Symfony to version 7.1.4.
- Added rector fix script to `composer.json`.
- Replaced links in tutorials.
- Updated tutorials.
- Internal changes in `PdfDocument` and `PdfViewerPreferences` classes.
- Added `SymfonyInsight` quality check tool, configuration and badge.
- Added descriptions to `PdfBlendMode` enumeration.
- Minor changes in tutorials.

## 2.0.6 - 2024-08-14

- Added `PdfSectorTrait` trait and the corresponding tutorial.

## 2.0.5 - 2024-08-14

- Added `PdfRotationTrait` trait and the corresponding tutorial.

## 2.0.4 - 2024-08-14

- Added `PdfEllipseTrait` trait and the corresponding tutorial.
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
- Added a unique CI action and removed all other actions.
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
  - Corrected `multiCell()` function to handle correct borders.
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

- Replaced static by self as the return value.
- Added clean text function.
- Corrected put image.
- Corrected the image soft mask.
- Initialize properties. Added left and right margins.
- Replaced self by static.
- Added test for `PdfFontStyle`.
- Removed `replace` entries in the `composer.json`.

## 1.8.4-alpha-6 - 2024-02-13

- Update `.styleci.yml`.
- Added `CHANGELOG` file.
- Added tests.
- Added isLink function.
- Added the new line constant.
- Updated PHP doc.

## 1.8.4-alpha-5 - 2024-02-11

- Updated PHP-Stan to level 9.
- Added `PdfRotation` enumeration.
- Added tests.

## 1.8.4-alpha-4 - 2024-02-08

- Updated font directory for tests.
- Replaced the border integer type by boolean type.

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
- The MakeFont now subsets fonts, which can greatly reduce font sizes.
- Added ToUnicode CMaps to improve text extraction.
- Added a parameter to AddPage() to rotate the page.
- Added a parameter to SetY() to indicate whether the x position should be reset
  or not.
- Added a parameter to Output() to specify the encoding of the name, and special
  characters are now properly encoded. Additionally, the order of the first two
  parameters was reversed to be more logical. However, the old order is still
  supported for compatibility.
- The Error() method now throws an exception.
- Adding contents before the first AddPage() or after Close() now raises an
  error.
- Outputting text with no font selected now raises an error.

## 1.7 - 2011-06-18

- The MakeFont utility has been completely rewritten and doesn't depend on
  ttf2pt1 anymore.
- Alpha channel is now supported for PNGs.
- When inserting an image, it is now possible to specify its resolution.
- The default resolution for images was increased from 72 to 96 dpi.
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
  HTTP server applied compression.

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
  longer necessary to define the `FPDF_FONTPATH` constant.
- The array $HTTP_SERVER_VARS is no longer used. It could cause trouble on
  PHP5-based configurations with the register_long_arrays option disabled.
- Fixed a problem related to Type1 font embedding, which caused trouble to some
  PDF processors.
- The file name sent to the browser could not contain a space character.
- The Cell() method could not print the number 0 (you had to pass the
  string '0').

## 1.52 - 2003-12-30

- Image() now displays the image at 72 dpi if no dimension is given.
- Output() takes a string as the second parameter to indicate the destination.
- Open() is now called automatically by AddPage().
- Inserting remote JPEG images doesn't generate an error any longer.
- The decimal separator is forced to dot in the constructor.
- Added several encodings (Turkish, Thai, Hebrew, Ukrainian and Vietnamese).
- The last line of a right-aligned MultiCell() was not correctly aligned if it
  was terminated by a carriage return.
- No more error messages about already sent headers when outputting the PDF to
  the standard output from the command line.
- The underlining was going too far for text containing characters \, (or).
- $HTTP_ENV_VARS has been replaced by $HTTP_SERVER_VARS.

## 1.51 - 2002-08-03

- Type1 font support.
- Added Baltic encoding.
- The class now works internally in points with the origin at the bottom order
  to avoid two bugs occurring with Acrobat 5:*. The line thickness was too
  large when printed on Windows 98 SE, and ME TrueType fonts
  didn't appear immediately inside the plug-in (a substitution font was used),
  one had to cause a window refresh to make them show up.
- It is no longer necessary to set the decimal separator as the dot to produce
  valid documents.
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
- Modification of SetDisplayMode() to select the page layout.
- The border parameter of MultiCell() now lets choosing borders to draw
  as Cell().
- When a document contains no page, Close() now calls AddPage() instead of
  causing a fatal error.

## 1.41 - 2002-03-13

- Fixed SetDisplayMode() which no longer worked (the PDF viewer used its
  default display).

## 1.4 - 2002-03-02

- PHP3 is no longer supported.
- Page compression (SetCompression()).
- Choice of page format and possibility to change orientation inside the
  document.
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
- During the automatic page break, the current abscissa is now retained.

## 1.11 - 2001-10-20

- PNG support doesn't require PHP4/zlib anymore. Data are now put directly
  into `PDF` without any decompression/recompression stage.
- Image insertion now works correctly even with the magic_quotes_runtime option
  set to on.

## 1.1 - 2001-10-07

- JPEG and PNG image support.

## 1.01 - 2001-10-03

- Fixed a bug involving page break: in case when Header() doesn't specify a
  font, the one from the previous page was not restored and produced an
  incorrect document.

## 1.0 - 2001-09-17

- First version.
