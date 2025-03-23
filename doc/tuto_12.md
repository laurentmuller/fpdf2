# Dash lines

Since version `3.0.10`, a trait allows drawing dash lines.

**Note:** The code is inspired from this given
[FPDF script](https://www.fpdf.org/en/script/script33.php) created by
Okita Yukihiro.

**Definition:**

```php
setDashPattern(float $dashes, float $gaps)
```

**Parameters:**

- `$dashes`: the length of dashes.
- `$gaps`: the length of gaps.

**Usage:**

To use it, create a derived class and use the `PdfDashTrait` trait:

```php
use fpdf\PdfDocument;
use fpdf\Traits\PdfDashTrait;

class DashDocument extends PdfDocument
{
    use PdfDashTrait;
}

// instanciation of inherited class
$pdf = new DashDocument();
$pdf->addPage();
$pdf->setLineWidth(0.5);
$pdf->setDashPattern(3.0, 2.0);
$pdf->line(10, 10, 72, 10);
$pdf->rect(10, 15, 62, 20);
```

To restore normal drawing, call:

```php
$pdf->resetDashPattern();
```

**Result:**

![Result](images/dashes.png)

**Shortcut:**

A method allows drawing a dashed rectangle within a single call:

```php
dashedRect(
    float $x,
    float $y,
    float $w,
    float $h,
    float $dashes,
    ?float $lineWidth = null
)
```

After this call, the dashed pattern and the line width are restored.

**Parameters:**

- `$x`: the abscissa of the upper-left corner.
- `$y`: the ordinate of the upper-left corner.
- `$w`: the width.
- `$h`: the height.
- `$dashes`: the length of dashes and gaps.
- `$lineWidth`: the line width or `null` to use the current line
  width.

**See also:**

- [Examples](examples.md)
- [Home](../README.md)
