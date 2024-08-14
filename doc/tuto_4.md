# Multi-columns

This example is a variant of the previous one showing how to lay the text across
multiple columns.

```php
use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfTextAlignment;
use fpdf\PdfBorder;
use fpdf\PdfDocument;

class CustomDocument extends PdfDocument
{
    // current column
    private int $col = 0;
    // ordinate of column start
    private float $currentY = 0.0;

    public function header(): void
    {
        // page header
        $title = $this->getTitle();
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD, 15);
        $width = $this->getStringWidth($title) + 6.0;
        $this->setX((210.0 - $width) / 2);
        $this->setDrawColor(0, 80, 180);
        $this->setFillColor(230, 230, 0);
        $this->setTextColor(220, 50, 50);
        $this->setLineWidth(1);
        $this->cell($width, 9, $title, PdfBorder::all(), PdfMove::NEW_LINE, PdfTextAlignment::CENTER, true);
        $this->lineBreak(10);
        // save ordinate
        $this->currentY = $this->getY();
    }
    
    public function footer(): void
    {
        // page footer
        $this->setY(-15);
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::ITALIC, 8);
        $this->setTextColor(128);
        $this->cell(null, 10, \sprintf('Page %d', $this->getPage()), PdfBorder::none(), PdfMove::RIGHT, PdfTextAlignment::CENTER);
    }

    public function setCol(int $col): void
    {
        // set position at a given column
        $this->col = $col;
        $x = 10.0 + (float) $col * 65.0;
        $this->setLeftMargin($x);
        $this->setX($x);
    }

    public function chapterBody(string $file): void
    {
        // read text file
        $content = (string) \file_get_contents($file);
        // font
        $this->setFont(PdfFontName::TIMES, PdfFontStyle::REGULAR, 12);
        // output text in a 6 cm width column
        $this->multiCell(60, 5, $content);
        $this->lineBreak();
        // mention
        $this->setFont(style: PdfFontStyle::ITALIC);
        $this->cell(null, 5, '(end of excerpt)');
        // go back to first column
        $this->setCol(0);
    }

    public function chapterTitle(int $num, string $title): void
    {
        // title
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR, 12);
        $this->setFillColor(200, 220, 255);
        $this->cell(null, 6, \sprintf('Chapter %d. %s', $num, $title), PdfBorder::none(), PdfMove::NEW_LINE, PdfTextAlignment::LEFT, true);
        $this->lineBreak(4);
        // save ordinate
        $this->currentY = $this->getY();
    }

    public function isAutoPageBreak(): bool
    {
        // method accepting or not automatic page break
        if ($this->col < 2) {
            // go to next column
            $this->setCol($this->col + 1);
            // set ordinate to top
            $this->setY($this->currentY);
            // keep on page
            return false;
        }

        // go back to first column
        $this->setCol(0);

        // page break
        return true;
    }

    public function printChapter(int $num, string $title, string $file): void
    {
        // add chapter
        $this->addPage();
        $this->chapterTitle($num, $title);
        $this->chapterBody($file);
    }
}

$pdf = new CustomDocument();
$pdf->setAuthor('Jules Verne');
$pdf->setTitle('20000 Leagues Under the Seas');
$pdf->printChapter(1, 'A RUNAWAY REEF', '20k_c1.txt');
$pdf->printChapter(2, 'THE PROS AND CONS', '20k_c2.txt');
$pdf->output();
```

**See also:**

- [Minimal example](tuto_1.md)
- [Header, footer, page break and image](tuto_2.md)
- [Line breaks and colors](tuto_3.md)
- [Tables](tuto_5.md)
- [Bookmarks](tuto_6.md)
- [Transparency](tuto_7.md)
- [Circles and ellipses](tuto_8.md)
- [Home](../README.md)
