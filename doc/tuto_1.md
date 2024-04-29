# Minimal example

Let's start with the classic example:

```php
use fpdf\PdfDocument;
use fpdf\PdfFontName;
use fpdf\PdfFontStyle;

$pdf = new PdfDocument();
$pdf->addPage();
$pdf->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD, 16);
$pdf->cell(40, 10, 'Hello World!');
$pdf->output();
```

After including the library files, we create an PdfDocument object. The
constructor is used here with the default values: pages are in A4 portrait,
and the unit of measure is millimeter. It could have been specified explicitly
with:

```php
$pdf = new PdfDocument(PdfOrientation::PORTRAIT, PdfUnit::MILLIMETER, PdfPageSize::A4);
```

It is possible to use landscape, other page sizes (such as Letter and Legal)
and units.

There is no page now, so we have to add one with `addPage()`. The origin is in
the upper-left corner and the current position is by default set at 1 cm from
the borders; the margins can be changed with `setMargins()`.

Before we can print text, it is mandatory to select a font with `setFont()`.
We choose Arial bold 16:

```php
$pdf->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD, 16);
```

We could have specified italics with `PdfFontStyle::ITALIC`, underlined with
`PdfFontStyle::UNDERLINE` or a regular font (or any combination). Note that the
font size is given in points, not millimeters (or another user unit); it is the
only exception. The other standard fonts are `PdfFontName::TIMES`,
`PdfFontName::COURIER`, `PdfFontName::SYMBOL` and `PdfFontName::ZAPFDINGBATS`.

We can now print a cell with `cell()`. A cell is a rectangular area, possibly
framed, which contains a line of text. It is output at the current position.
We specify its dimensions, its text (centered or aligned), if borders should be
drawn, and where the current position moves after it (to the right, below or to
the beginning of the next line). To add a frame, we would do this:

```php
$pdf->cell(40, 10, 'Hello World!');
```

To add a new cell next to it with centered text and go to the next line,
we would do:

```php
$pdf->cell(60, 10, 'Powered by FPDF2.', PdfBorder::none(), PdfMove::NEW_LINE, PdfTextAlignment.CENTER);
```

**Remark:** The line break can also be done with `lineBreak()`. This method
additionally allows specifying the height of the break.

Finally, the document is closed and sent to the browser with `output()`. We
could have saved it to a file by passing the appropriate parameters.

**Caution:** In case when the PDF is sent to the browser, nothing else must be
output by the script, neither before nor after (no HTML, not even a space or a
carriage return). If you send something before, you will get the error
message:

```yaml
"Some data has already been output, can't send PDF file"
```

If you send something after, the document might not display.

**See also:**

- [Header, footer, page break and image](tuto_2.md)
- [Line breaks and colors](tuto_3.md)
- [Multi-columns](tuto_4.md)
- [Tables](tuto_5.md)
- [Home](../README.md)
