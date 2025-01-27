# Transparency

Since version `2.0.3`, transparency can be added within the `PdfDocument`.

**Note:** The code is inspired from this given
[FPDF script](http://www.fpdf.org/en/script/script74.php) created by
Martin Hall-May.

To use it, create a derived class and use the `PdfTransparencyTrait` trait:

```php
use fpdf\Enum\PdfBlendMode;
use fpdf\PdfDocument;
use fpdf\Traits\PdfTransparencyTrait;

class TransparencyDocument extends PdfDocument
{
    use PdfTransparencyTrait;
}

// instanciation of inherited class
$pdf = new TransparencyDocument();
$pdf->addPage();
// output an image with no transparency
$pdf->image('logo.png', 10, 20);
// set alpha with a 50% of transparency
$pdf->setAlpha(0.5, PdfBlendMode::NORMAL);
// output an image with transparency
$pdf->image('logo.png', 50, 20);
// restore alpha to default
$pdf->resetAlpha();
```

**Result:**

![Result](images/transparency.png)

**See also:**

- [Examples](examples.md)
- [Home](../README.md)
