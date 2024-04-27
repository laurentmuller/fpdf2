# FPDF2

**This repository is clone of [fpdf.org](http://www.fpdf.org) with typed
variables, enumerations and PHP 8.2 dependencies.**

`PdfDocument` is a PHP class, which allows to generate PDF files with pure PHP.
**F** from FPDF2 stands for **Free**: you may use it for any kind of usage and
modify it to suit your needs.

## Installation with [Composer](https://packagist.org/packages/laurentmuller/fpdf2)

If you're using Composer to manage dependencies, you can use

```powershell
composer require laurentmuller/fpdf2:^1.8
```

Or you can include the following in your `composer.json` file:

```json
{
    "require": {
        "laurentmuller/fpdf2": "^1.8"
    }
}
```

**Usage:**

```php
$pdf = new PdfDocument();
$pdf->addPage();
$pdf->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD, 16);
$pdf->cell(40, 10, 'Hello World!');
$pdf->output();
```

**Tutorials:**

- [Minimal example](doc/tuto_1.md)
- [Header, footer, page break and image](doc/tuto_2.md)
- [Line breaks and colors](doc/tuto_3.md)
- [Multi-columns](doc/tuto_4.md)
- [Tables](doc/tuto_5.md)

## Actions

[![PHP-CS-Fixer](https://github.com/laurentmuller/fpdf2/actions/workflows/php-cs-fixer.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/php-cs-fixer.yaml)
[![PHPStan](https://github.com/laurentmuller/fpdf2/actions/workflows/php_stan.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/php_stan.yaml)
[![PHPUnit](https://github.com/laurentmuller/fpdf2/actions/workflows/php_unit.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/php_unit.yaml)
[![Psalm](https://github.com/laurentmuller/fpdf2/actions/workflows/pslam.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/pslam.yaml)
[![Rector](https://github.com/laurentmuller/fpdf2/actions/workflows/rector.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/rector.yaml)
[![Lint](https://github.com/laurentmuller/fpdf2/actions/workflows/lint.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/lint.yaml)
[![StyleCI](https://github.styleci.io/repos/752676081/shield?branch=main)](https://github.styleci.io/repos/752676081?branch=main)
[![codecov](https://codecov.io/gh/laurentmuller/fpdf2/graph/badge.svg?token=16I8LCYRRS)](https://codecov.io/gh/laurentmuller/fpdf2)
