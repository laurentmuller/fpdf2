# Attachments

Since version `2.0.13`, a trait allows attaching files to the PDF.

**Note:** The code is inspired from this given
[FPDF script](http://www.fpdf.org/en/script/script95.php) created by
Oliver.

**Definition:**

```php
addAttachment(string $file, string $name = '', string $description = '')
```

**Parameters:**

- `$file`: The path to the file to be attached.
- `$name`: An optional alternative file name to be used for the attachment
  The default value is the base name of the file.
- `$description`: An optional description for the file contents.

**Usage:**

To use it, create a derived class and use the `PdfAttachmentTrait` trait:

```php
use fpdf\Enums\PdfPageMode;
use fpdf\PdfDocument;
use fpdf\Traits\PdfAttachmentTrait;

class AttachmentDocument extends PdfDocument
{
    use PdfAttachmentTrait;
}

$file = "attached.txt";

$pdf = new AttachmentDocument();
$pdf->addPage();
$pdf->addAttachment($file);
// force the PDF viewer to open the attachment pane
$pdf->setPageMode(PdfPageMode::USE_ATTACHMENTS);
$pdf->output();
```

**See also:**

- [Examples](examples.md)
- [Home](../README.md)
