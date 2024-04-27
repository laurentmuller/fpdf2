# Multi-columns

This example is a variant of the previous one showing how to lay the text across multiple columns.

```php
class CustomDocument extends PdfDocument
{
    // Current column
    private int $col = 0;
    // Ordinate of column start
    private float $y0 = 0.0;      
    
    function header(): void
    {
        // Page header
        global $title = '';
    
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD, 15);
        $w = $this->getStringWidth($title) + 6;
        $this->setX((210 - $w) / 2);
        $this->setDrawColor(0, 80, 180);
        $this->setFillColor(230, 230, 0);
        $this->setTextColor(220, 50, 50);
        $this->setLineWidth(1);
        $this->cell($w, 9, $title, PdfBorder::all(), PdfMove::NEW_LINE, PdfTextAlignment.CENTER, true);
        $this->lineBreak(10);
        // Save ordinate
        $this->y0 = $this->getY();
    }
    
    function footer(): void
    {
        // Page footer
        $this->setY(-15);
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::ITALIC, 8);
        $this->setTextColor(128);
        $this->cell(0, 10, 'Page ' . $this->PageNo(), PdfBorder::NONE(), PdfMove::RIGHT, PdfTextAlignment.CENTER);
    }
    
    function setCol($col): void
    {
        // Set position at a given column
        $this->col = $col;
        $x = 10 + $col * 65;
        $this->setLeftMargin($x);
        $this->setX($x);
    }
    
    function isAutoPageBreak(): bool
    {
        // Method accepting or not automatic page break
        if($this->col < 2)
        {
            // Go to next column
            $this->setCol($this->col + 1);
            // Set ordinate to top
            $this->setY($this->y0);
            // Keep on page
            return false;
        } else {
            // Go back to first column
            $this->setCol(0);
            // Page break
            return true;
        }
    }
    
    function chapterTitle(int $num, string $label): void
    {
        // Title
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR, 12);
        $this->setFillColor(200, 220, 255);
        $this->cell(0, 6, "Chapter $num : $label", PdfBorder::NONE(), PdfMove::NEW_LINE, PdfTextAlignment.LEFT, true);
        $this->lineBreak(4);
        // Save ordinate
        $this->y0 = $this->getY();
    }
    
    function chapterBody(string $file): void
    {
        // Read text file
        $txt = \file_get_contents($file);
        // Font
        $this->setFont(PdfFontName::TIMES, PdfFontStyle::REGULAR, 12);
        // Output text in a 6 cm width column
        $this->multiCell(60, 5, $txt);
        $this->lineBreak();
        // Mention
        $this->setFont(null, PdfFontStyle::ITALIC);
        $this->cell(0,5, '(end of excerpt)');
        // Go back to first column
        $this->setCol(0);
    }
    
    function printChapter(int $num, string $title, string $file): void
    {
        // Add chapter
        $this->addPage();
        $this->chapterTitle($num,$title);
        $this->chapterBody($file);
    }
}

$pdf = new CustomDocument();
$pdf->setTitle('20000 Leagues Under the Seas');
$pdf->setAuthor('Jules Verne');
$pdf->printChapter(1, 'A RUNAWAY REEF', '20k_c1.txt');
$pdf->printChapter(2, 'THE PROS AND CONS', '20k_c2.txt');
$pdf->output();
```

**See also:**

- [Minimal example](tuto_1.md)
- [Header, footer, page break and image](tuto_2.md)
- [Line breaks and colors](tuto_3.md)
- [Tables](tuto_5.md)
- [Home](../README.md)
