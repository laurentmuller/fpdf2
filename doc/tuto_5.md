# Tables

This tutorial shows different ways to make tables.

```php
class CustomDocument extends PdfDocument
{
    // Load data
    function loadData(string $file): array
    {
        // Read file lines
        $data = [];
        $lines = file($file);
        foreach($lines as $line) {
            $data[] = explode(';',trim($line));
        }
        
        return $data;
    }
    
    // Simple table
    function basicTable(array $header, array $data): void
    {
        // Header
        foreach($header as $col) {
            $this->cell(40, 7, $col, PdfBorder::all());
        }
        $this->lineBreak();
        // Data
        foreach($data as $row)
        {
            foreach($row as $col) {
                $this->cell(40, 6, $col, PdfBorder::all());
            }
            $this->lineBreak();
        }
    }
    
    // Better table
    function improvedTable(array $header, array $data): void
    {
        // Column widths
        $w = [40, 35, 40, 45];
        // Header
        for($i = 0; $i < count($header); $i++) {
            $this->cell($w[$i], 7, $header[$i], PdfBorder::all(), PdfMove::RIGHT, PdfTextAlignment::CENTER);
        }
        $this->lineBreak();
        // Data
        $border = PdfBorder::leftRight();
        foreach($data as $row)
        {
            $this->cell($w[0], 6, $row[0], $border);
            $this->cell($w[1], 6, $row[1], $border);
            $this->cell($w[2], 6, \number_format($row[2]), $border, PdfMove::RIGHT, PdfTextAlignment::RIGHT);
            $this->cell($w[3], 6, \number_format($row[3]), $border, PdfMove::RIGHT, PdfTextAlignment::RIGHT);
            $this->lineBreak();
        }
        // Closing line
        $this->cell(array_sum($w), 0, '', PdfBorder::top());
    }
    
    // Colored table
    function fancyTable(array $header, array $data): void
    {
        // Colors, line width and bold font
        $this->setFillColor(255, 0, 0);
        $this->setTextColor(255);
        $this->setDrawColor(128, 0, 0);
        $this->setLineWidth(0.3);
        $this->setFont('', PdfFontStyle::BOLD);
        // Header
        $w = [40, 35, 40, 45];
        for($i=0; $i < count($header); $i++) {
            $this->cell($w[$i], 7, $header[$i], PdfBorder::all(), PdfMove::RIGHT, PdfTextAlignment::CENTER,true);
        }
        $this->lineBreak();
        // Color and font restoration
        $this->setFillColor(224, 235, 255);
        $this->setTextColor(0);
        $this->setFont('');
        // Data
        $fill = false;
        $border = PdfBorder::leftRight();
        foreach($data as $row)
        {
            $this->cell($w[0], 6, $row[0], $border, PdfMove::RIGHT, PdfTextAlignment::LEFT, $fill);
            $this->cell($w[1], 6, $row[1], $border, PdfMove::RIGHT, PdfTextAlignment::LEFT, $fill);
            $this->cell($w[2], 6, \number_format($row[2]), $border, PdfMove::RIGHT, PdfTextAlignment::RIGHT, $fill);
            $this->cell($w[3], 6, \number_format($row[3]), $border, PdfMove::RIGHT, PdfTextAlignment::RIGHT, $fill);
            $this->lineBreak();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

$pdf = new CustomDocument();
// Column headings
$header = ['Country', 'Capital', 'Area (sq km)', 'Pop. (thousands)'];
// Data loading
$data = $pdf->loadData('countries.txt');
$pdf->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR, 14);
$pdf->addPage();
$pdf->basicTable($header,$data);
$pdf->addPage();
$pdf->improvedTable($header,$data);
$pdf->addPage();
$pdf->fancyTable($header,$data);
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
in the loop, in which case we use LRB for the border parameter; or, as done
here, add the line once the loop is over.

The third table is similar to the second one but uses colors. Fill, text and
line colors are simply specified. Alternate coloring for rows is obtained by
using alternatively transparent and filled cells.

**See also:**

- [Minimal example](tuto_1.md)
- [Header, footer, page break and image](tuto_2.md)
- [Line breaks and colors](tuto_3.md)
- [Multi-columns](tuto_4.md)
- [Home](../README.md)
