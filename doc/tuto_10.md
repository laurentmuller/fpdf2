# Sector

Since version `2.0.6`, A trait allows drawing sector of a circle. It can be
used, for example, to render a pie chart.

**Note:** The code is inspired from this given
[FPDF](http://www.fpdf.org/en/script/script19.php) script created by
Maxime Delorme.

All angle parameters are expressed in degrees ('&deg;').

To use it, create a derived class and use the `PdfSectorTrait` trait:

```php
use fpdf\PdfDocument;
use fpdf\Traits\PdfSectorTrait;

class SectorDocument extends PdfDocument
{
    use PdfSectorTrait;
}

$radius = 40;
$centerX = 105;
$centerY = 60;

// instanciation of inherited class
$pdf = new SectorDocument();
$pdf->addPage();
// first sector
$pdf->setFillColor(120, 120, 255);
$pdf->sector($centerX, $centerY, $radius, 20, 120);
// second sector
$pdf->setFillColor(120, 255, 120);
$pdf->sector($centerX, $centerY, $radius, 120, 250);
// third sector
$pdf->setFillColor(255, 120, 120);
$pdf->sector($centerX, $centerY, $radius, 250, 20);

$pdf->output();
```

**Result:**

![Result](images/sector.png)

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
- [Home](../README.md)
