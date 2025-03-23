# Line breaks and colors

Let's continue with an example, which prints justified paragraphs. It also
illustrates the use of colors.

```php
use fpdf\Color\PdfGrayColor;
use fpdf\Color\PdfRgbColor;
use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\Enums\PdfMove;
use fpdf\Enums\PdfTextAlignment;
use fpdf\PdfBorder;
use fpdf\PdfDocument;

class CustomDocument extends PdfDocument
{
    public function header(): void
    {
        // Arial bold 15pt
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD, 15);
        // calculate width of title and position
        $title = $this->getTitle();
        $width = $this->getStringWidth($title) + 6.0;
        $this->setX(($this->width - $width) / 2.0);
        // colors of frame, background and text
        $this->setDrawColor(PdfRgbColor::instance(0, 80, 180));
        $this->setFillColor(PdfRgbColor::instance(230, 230, 0));
        $this->setTextColor(PdfRgbColor::instance(220, 50, 50));
        // thickness of frame (1 mm)
        $this->setLineWidth(1);
        // title
        $this->cell($width, 9, $title, PdfBorder::all(), PdfMove::NEW_LINE, PdfTextAlignment::CENTER, true);
        // line break
        $this->lineBreak(10);
    }

    public function footer(): void
    {
        // position at 1.5 cm from bottom
        $this->setY(-15);
        // Arial italic 8pt
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::ITALIC, 8);
        // text color in gray
        $this->setTextColor(PdfGrayColor::instance(128));
        // page number
        $this->cell(null, 10, \sprintf('Page %d', $this->getPage()), PdfBorder::none(), PdfMove::RIGHT, PdfTextAlignment::CENTER);
    }
    
    public function chapterBody(string $file): void
    {
        // read text file
        $content = (string) \file_get_contents($file);
        // Times 12pt
        $this->setFont(PdfFontName::TIMES, PdfFontStyle::REGULAR, 12);
        // output justified text
        $this->multiCell(null, 5, $content);
        // line break
        $this->lineBreak();
        // mention in italics
        $this->setFont(null, PdfFontStyle::ITALIC);
        $this->cell(null, 5, '(end of excerpt)');
    }

    public function chapterTitle(int $num, string $title): void
    {
        // Arial 12pt
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR, 12);
        // background color
        $this->setFillColor(PdfRgbColor::instance(200, 220, 255));
        // title
        $this->cell(null, 6, \sprintf('Chapter %d. %s', $num, $title), PdfBorder::none(), PdfMove::NEW_LINE, PdfTextAlignment::LEFT, true);
        // line break
        $this->lineBreak(4);
    }

    public function printChapter(int $num, string $title, string $file): void
    {
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

The `getStringWidth()` method allows determining the length of a string in the
current font, which is used here to calculate the position and the width of the
frame surrounding the title. Then colors are set (via `setDrawColor()`,
`setFillColor()` and `setTextColor()`) and the thickness of the line is set to
1 mm (instead of 0.2 by default) with `setLineWidth()`. Finally, we output the
cell (the last parameter true indicates that the background must be filled).

The method used to print the paragraphs is `multiCell()`. Each time a line
reaches the right extremity of the cell or a carriage return character is met,
a line break is issued and a new cell is automatically created under
the current one. Text is justified by default.

Two document properties are defined: the title (`setTitle()`) and the author
(`setAuthor()`). There are several ways to view them in Adobe Reader. The first
one is to open the file directly with the reader, go to the File menu and choose
the Properties option. The second one, also available from the plug-in, is to
right-click and select Document Properties. The third method is to type the
`Ctrl + D` key combination.

**See also:**

- [Examples](examples.md)
- [Home](../README.md)
