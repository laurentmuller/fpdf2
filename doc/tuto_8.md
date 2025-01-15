# Circles and ellipses

Since version `2.0.4`, circles and ellipses can be drawn within
the `PdfDocument`.

**Note:** The code is inspired from this given
[FPDF script](http://www.fpdf.org/en/script/script6.php) created by Olivier.

To use it, create a derived class and use the `PdfEllipseTrait` trait:

```php
use fpdf\Enums\PdfRectangleStyle;
use fpdf\PdfDocument;
use fpdf\Traits\PdfEllipseTrait;

class EllipseDocument extends PdfDocument
{
    use PdfEllipseTrait;
}

// instanciation of inherited class
$pdf = new EllipseDocument();
// output an ellipse and a circle with border only
$pdf->setDrawColor(255, 0, 0);
$pdf->ellipse(30, 220, 20, 10);
$pdf->circle(65, 220, 10);
// output an ellipse and a circle with border and fill colors
$pdf->setFillColor(0, 255, 0);
$pdf->circle(65, 245, 10, PdfRectangleStyle::BOTH);
$pdf->ellipse(30, 245, 20, 10, PdfRectangleStyle::BOTH);
```

**Result:**

![Result](images/ellipses.png)

**See also:**

- [Examples](examples.md)
- [Home](../README.md)
