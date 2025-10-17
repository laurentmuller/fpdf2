# Transparency

Since version `2.0.3`, transparency can be added within the `PdfDocument`.

**Note:** The code is inspired from this given
[FPDF script](http://www.fpdf.org/en/script/script74.php) created by
Martin Hall-May.

**Definition:**

```php
setAlpha(float $alpha, PdfBlendMode $blendMode = PdfBlendMode::NORMAL)
```

**Parameters:**

- `$alpha`: The alpha channel from 0.0 (fully transparent) to 1.0
   (fully opaque).
- `$blendMode`: The blend mode as defined below.

**Blend mode:**

- `COLOR`: Creates a color with the hue and saturation of the source color
           and the luminosity of the backdrop color. This preserves the gray
           levels of the backdrop and is useful for coloring monochrome
           images or tinting color images.
- `COLOR_BURN`: The annotation color is reflected by darkening the color of the
   background content.
- `COLOR_DODGE`: The annotation color is reflected by brightening the color of
  the background content.
- `DARKEN`: Replaces only the areas that are lighter than the blend color.
  Areas darker than the blend color don’t change.
- `DIFFERENCE`: The darker color of the background content color and the
  annotation color is subtracted from the lighter color.
- `EXCLUSION`: This is the same effect as difference but with low contrast.
- `HARD_LIGHT`: Multiplies or screens the colors, depending on the background
  content color. The effect is similar to that of shining a harsh spotlight on
  the background content.
- `HUE`: Creates a color with the hue of the source color and the saturation
  and luminosity of the backdrop color.
- `LIGHTEN`: Replaces only pixels that are darker than the blend color. Areas
  lighter than the blend color don’t change.
- `LUMINOSITY`: Creates a color with the luminosity of the source color and the
  hue and saturation of the backdrop color. This produces an inverse effect on
  that of the color mode.
- `MULTIPLY`: Multiplies the base color by the blend color, resulting in darker
  colors.
- `NORMAL`: Applies color normally, with no interaction with the base colors.
- `OVERLAY`: Multiplies or screens the colors, depending on the base colors.
- `SATURATION`: Creates a color with the saturation of the source color and the
  hue and luminosity of the backdrop color. Painting with this mode in an area
  of the backdrop that is a pure gray (no saturation) produces no change.
- `SCREEN`: Multiplies the inverse of the blend color by the base color,
  resulting in a bleaching effect.
- `SOFT_LIGHT`: Darkening or lightening of colors is based on the background
  content color. The effect is similar to that of shin.

**Usage:**

To use it, create a derived class and use the `PdfTransparencyTrait` trait:

```php
use fpdf\Enum\PdfBlendMode;
use fpdf\PdfDocument;
use fpdf\Traits\PdfTransparencyTrait;

class PdfTransparencyDocument extends PdfDocument
{
    use PdfTransparencyTrait;
}

// instanciation of inherited class
$pdf = new PdfTransparencyDocument();
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
