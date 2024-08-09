# Header, footer, page break and image

Here's a two-page example with a header, a footer and a logo:

```php
use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfMove;
use fpdf\PdfBorder;
use fpdf\PdfDocument;

class CustomDocument extends PdfDocument
{
    // page header
    public function header(): void
    {
        // logo
        $this->image('logo.png', 10, 6, 30);
        // Arial bold 15pt
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD, 15);
        // move to the right
        $this->cell(80);
        // title
        $this->cell(30, 10, 'Title', PdfBorder::all(), PdfMove::RIGHT, PdfTextAlignment::CENTER);
        // line break
        $this->lineBreak(20);
    }

    // page footer
    public function footer(): void
    {
        // position at 1.5 cm from bottom
        $this->setY(-15);
        // Arial italic 8pt
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::ITALIC, 8);
        // page number
        $this->cell(null, 10, \sprintf('Page %d/{nb}', $this->getPage()), PdfBorder::none(), PdfMove::RIGHT, PdfTextAlignment::CENTER);
    }
}

// instanciation of inherited class
$pdf = new CustomDocument();
$pdf->setFont(PdfFontName::TIMES, PdfFontStyle::REGULAR, 12);
$pdf->setAliasNumberPages();
$pdf->addPage();
for ($i = 1; $i <= 40; ++$i) {
    $pdf->cell(null, 10, \sprintf('Printing line number %d', $i), PdfBorder::none(), PdfMove::NEW_LINE);
}
$pdf->output();
```

**See also:**

- [Minimal example](tuto_1.md)
- [Line breaks and colors](tuto_3.md)
- [Multi-columns](tuto_4.md)
- [Tables](tuto_5.md)
- [Home](../README.md)
