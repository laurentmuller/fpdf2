# Header, footer, page break and image

Here's a two-page example with a header, a footer and a logo:

```php
class CustomDocument extends PdfDocument
{
    // Page header
    function header(): void
    {
        // Logo
        $this->image('logo.png', 10, 6, 30);
        // Arial bold 15
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD, 15);
        // move to the right
        $this->cell(80);
        // title
        $this->cell(30, 10, 'Title', PdfBorder::all(), PdfMove::RIGHT, PdfTextAlignment.CENTER);
        // line break
        $this->lineBreak(20);
    }
    
    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->setY(-15);
        // Arial italic 8
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::ITALIC, 8);
        // Page number
        $this->cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', PdfBorder::none(), PdfMove::RIGHT, PdfTextAlignment.CENTER);
    }
}

// Instanciation of inherited class
$pdf = new CustomDocument();
$pdf->setAliasNumberPages()
    ->setFont(PdfFontName::TIMES, PdfFontStyle::REGULAR, 12)
    ->addPage();
for($i = 1; $i <= 40; $i++) {
    $pdf->cell(0, 10, 'Printing line number ' . $i, PdfBorder::none(), PdfMove::NEW_LINE);
}
$pdf->output();
```

**See also:**

- [Minimal example](tuto_1.md)
- [Line breaks and colors](tuto_3.md)
- [Multi-columns](tuto_4.md)
- [Tables](tuto_5.md)
- [Home](../README.md)
