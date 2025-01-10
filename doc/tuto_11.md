# Attachments

Since version `2.0.13`, a trait allows attaching files to the PDF.

**Note:** The code is inspired from this given
[FPDF script](http://www.fpdf.org/en/script/script95.php) created by
Oliver.

The `openAttachmentPane()` method is also provided to force the PDF viewer to open
the attachment pane when the document is loaded.

To use it, create a derived class and use the `PdfAttachmentTrait` trait:

```php
use fpdf\PdfDocument;
use fpdf\Traits\PdfAttachmentTrait;

class AttachmentDocument extends PdfDocument
{
    use PdfAttachmentTrait;
}

$file = "test/attached.txt";

$pdf = new AttachmentDocument();
$pdf->addPage();
$document->attach($file);
$document->openAttachmentPane();

$pdf->output();
```

**Result:**

![Result](http://www.fpdf.org/en/script/ex95.pdf)

**See also:**

- [Minimal example](tuto_1.md)
- [Header, footer, page break and image](tuto_2.md)
- [Line breaks and colors](tuto_3.md)
- [Multi-columns](tuto_4.md)
- [Tables](tuto_5.md)
- [Bookmarks](tuto_6.md)
- [Transparency](tuto_7.md)
- [Circles and ellipses](tuto_8.md)
- [Rotation](tuto_9.md)
- [Sector](tuto_10.md)
- [Home](../README.md)
