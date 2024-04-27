# Line breaks and colors

Let's continue with an example, which prints justified paragraphs. It also
illustrates the use of colors.

```php
class CustomDocument extends PdfDocument
{
    function header(): void
    {
        global $title = '';
    
        // Arial bold 15
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD, 15);
        // Calculate width of title and position
        $w = $this->getStringWidth($title) + 6;
        $this->setX((210 - $w) / 2);
        // Colors of frame, background and text
        $this->setDrawColor(0, 80, 180);
        $this->setFillColor(230, 230, 0);
        $this->setTextColor(220, 50, 50);
        // Thickness of frame (1 mm)
        $this->setLineWidth(1);
        // Title
        $this->cell($w, 9, $title, PdfBorder::all(), PdfMove::NEW_LINE, PdfTextAlignment.CENTER, true);
        // Line break
        $this->lineBreak(10);
    }
    
    function footer(): void
    {
        // Position at 1.5 cm from bottom
        $this->setY(-15);
        // Arial italic 8
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::ITALIC, 8);
        // Text color in gray
        $this->setTextColor(128);
        // Page number
        $this->cell(0, 10, 'Page ' . $this->PageNo(), PdfBorder::NONE(), PdfMove::RIGHT, PdfTextAlignment.CENTER);
    }

    function chapterTitle(int $num, string $label): void
    {
        // Arial 12
        $this->setFont(PdfFontName::ARIAL, PdfFontStyle::REGULAR, 12);
        // Background color
        $this->setFillColor(200, 220, 255);
        // Title
        $this->cell(0, 6, "Chapter $num : $label", PdfBorder::NONE(), PdfMove::NEW_LINE, PdfTextAlignment.LEFT, true);
        // Line break
        $this->lineBreak(4);
    }
    
    function chapterBody(string $file): void
    {
        // Read text file
        $txt = \file_get_contents($file);
        // Times 12
        $this->setFont(PdfFontName::TIMES, PdfFontStyle::REGULAR, 12);
        // Output justified text
        $this->multiCell(0, 5, $txt);
        // Line break
        $this->lineBreak();
        // Mention in italics
        $this->setFont(null, PdfFontStyle::ITALIC);
        $this->cell(0,5,'(end of excerpt)');
    }
    
    function printChapter(int $num, string $title, string $file): void
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
a line break is issued and a new cell automatically created under the current
one. Text is justified by default.

Two document properties are defined: the title (`setTitle()`) and the author
(`setAuthor()`). There are several ways to view them in Adobe Reader. The first
one is to open the file directly with the reader, go to the File menu and choose
the Properties option. The second one, also available from the plug-in, is to
right-click and select Document Properties. The third method is to type the
`Ctrl + D` key combination.

**See also:**

- [Minimal example](tuto_1.md)
- [Header, footer, page break and image](tuto_2.md)
- [Multi-columns](tuto_4.md)
- [Tables](tuto_5.md)
- [Home](../README.md)
