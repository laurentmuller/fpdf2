# Tables

This tutorial shows different ways to make tables.

```php
use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfTextAlignment;
use fpdf\PdfBorder;
use fpdf\PdfDocument;

class CustomDocument extends PdfDocument
{

    // load data
    public function loadData(string $file): array
    {
        // read file lines
        $data = [];
        $lines = (array) \file($file);
        foreach ($lines as $line) {
            $data[] = \explode(';', \trim($line));
        }

        return $data;
    }

    // simple table
    public function basicTable(array $header, array $data): void
    {
        // header
        foreach ($header as $col) {
            $this->cell(40, 7, $col, PdfBorder::all());
        }
        $this->lineBreak();

        // data
        foreach ($data as $row) {
            foreach ($row as $col) {
                $this->cell(40, 6, $col, PdfBorder::all());
            }
            $this->lineBreak();
        }
    }

    // better table
    public function improvedTable(array $header, array $data): void
    {
        // column widths
        $widths = [40.0, 35.0, 40.0, 45.0];
        // header
        for ($i = 0, $len = \count($header); $i < $len; ++$i) {
            $this->cell($widths[$i], 7, $header[$i], PdfBorder::all(), align: PdfTextAlignment::CENTER);
        }
        $this->lineBreak();
        // data
        $border = PdfBorder::leftRight();
        foreach ($data as $row) {
            $this->cell($widths[0], 6, $row[0], $border);
            $this->cell($widths[1], 6, $row[1], $border);
            $this->cell($widths[2], 6, \number_format($row[2]), $border, align: PdfTextAlignment::RIGHT);
            $this->cell($widths[3], 6, \number_format($row[3]), $border, align: PdfTextAlignment::RIGHT);
            $this->lineBreak();
        }
        // closing line
        $this->cell(\array_sum($widths), 0, border: PdfBorder::top());
    }

    // colored table
    public function fancyTable(array $header, array $data): void
    {
        // colors, line width and bold font
        $this->setFillColor(255, 0, 0);
        $this->setTextColor(255);
        $this->setDrawColor(128, 0, 0);
        $this->setLineWidth(0.3);
        $this->setFont(style: PdfFontStyle::BOLD);
        // header
        $widths = [40.0, 35.0, 40.0, 45.0];
        for ($i = 0, $len = \count($header); $i < $len; ++$i) {
            $this->cell($widths[$i], 7, $header[$i], PdfBorder::all(), align: PdfTextAlignment::CENTER, fill: true);
        }
        $this->lineBreak();
        // color and font restoration
        $this->setFillColor(224, 235, 255);
        $this->setTextColor(0);
        $this->setFont(style: PdfFontStyle::REGULAR);
        // data
        $fill = false;
        $border = PdfBorder::leftRight();
        foreach ($data as $row) {
            $this->cell($widths[0], 6, $row[0], $border, PdfMove::RIGHT, PdfTextAlignment::LEFT, $fill);
            $this->cell($widths[1], 6, $row[1], $border, PdfMove::RIGHT, PdfTextAlignment::LEFT, $fill);
            $this->cell($widths[2], 6, \number_format($row[2]), $border, PdfMove::RIGHT, PdfTextAlignment::RIGHT, $fill);
            $this->cell($widths[3], 6, \number_format($row[3]), $border, PdfMove::RIGHT, PdfTextAlignment::RIGHT, $fill);
            $this->lineBreak();
            $fill = !$fill;
        }
        // closing line
        $this->cell(\array_sum($widths), 0, border: PdfBorder::top());
    }
}

$pdf = new CustomDocument();
// column headings
$header = ['Country', 'Capital', 'Area (sq km)', 'Pop. (thousands)'];
// data loading
$data = $pdf->loadData('countries.txt');
$pdf->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR, 14);
$pdf->addPage();
$pdf->basicTable($header, $data);
$pdf->addPage();
$pdf->improvedTable($header, $data);
$pdf->addPage();
$pdf->fancyTable($header, $data);
$pdf->output();
```

A table being just a collection of cells, it is natural to build one from them.
The first example is achieved in the most basic way possible: simple framed
cells, all the same size and left aligned. The result is rudimentary but
very quick to obtain.

The second table brings some improvements: each column has its own width,
headings are centered, and numbers right aligned. Moreover, horizontal lines
have been removed. This is done by means of the border parameter of the
`cell()` method, which specifies which sides of the cell must be drawn. Here we
want the left and right ones. It remains the problem of the horizontal line to
finish the table. There are two possibilities: either check for the last line
in the loop, in which case we use left, right and bottom for the border
parameter:

```php
new PdfBorder(true, false, true, true)
```

Or, as done here, add the line once the loop is over.

The third table is similar to the second one but uses colors. Fill, text and
line colors are simply specified. Alternate coloring for rows is obtained by
using alternatively transparent and filled cells.

**See also:**

- [Minimal example](tuto_1.md)
- [Header, footer, page break and image](tuto_2.md)
- [Line breaks and colors](tuto_3.md)
- [Multi-columns](tuto_4.md)
- [Bookmarks](tuto_6.md)
- [Transparency](tuto_7.md)
- [Circles and ellipses](tuto_8.md)
- [Rotation](tuto_9.md)
- [Sector](tuto_10.md)
- [Home](../README.md)
