# FPDF2

This repository is clone of [fpdf.org](http://www.fpdf.org) with typed variables,
enumerations and PHP 8.2 dependencies.

`PdfDocument` is a PHP class, which allows to generate PDF files with pure PHP.
**F** from FPDF2 stands for **Free**: you may use it for any kind of usage and
modify it to suit your needs.

## Installation

If you're using [Composer](https://getcomposer.org/) to manage dependencies,
you can use:

```bash
composer require laurentmuller/fpdf2
```

Alternatively, you can add the requirement `"laurentmuller/fpdf2":"^2.0"` to
your `composer.json` file and run `composer update`. This could be useful when
the installation of FPDF2 is not compatible with some currently installed
dependencies. Anyway, the previous option is the preferred way, since composer
can pick the best requirement constraint for you.

## Basic Usage

```php
use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontStyle;
use fpdf\PdfDocument;

$pdf = new PdfDocument();
$pdf->addPage();
$pdf->setFont(PdfFontName::ARIAL, PdfFontStyle::BOLD, 16);
$pdf->cell(40, 10, 'Hello World!');
$pdf->output();
```

## Tutorials

- [Minimal example](doc/tuto_1.md)
- [Header, footer, page break and image](doc/tuto_2.md)
- [Line breaks and colors](doc/tuto_3.md)
- [Multi-columns](doc/tuto_4.md)
- [Tables](doc/tuto_5.md)
- [Bookmarks](doc/tuto_6.md)
- [Transparency](doc/tuto_7.md)
- [Circles and ellipses](doc/tuto_8.md)
- [Rotation](doc/tuto_9.md)
- [Sector](doc/tuto_10.md)
- [Attachments](doc/tuto_11.md)

## Code Quality

[![SymfonyInsight](https://insight.symfony.com/projects/1db4f28c-c07c-4a5f-8006-2c63eb1e8851/mini.svg)](https://insight.symfony.com/projects/1db4f28c-c07c-4a5f-8006-2c63eb1e8851)
[![Codacy](https://app.codacy.com/project/badge/Grade/a70c684f21c446fb88658acf29fdafd5)](https://app.codacy.com/gh/laurentmuller/fpdf2/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)
[![PHP-Stan](https://img.shields.io/badge/PHPStan-Level%2010-brightgreen.svg?style=flat&logo=php)](https://phpstan.org/blog/find-bugs-in-your-code-without-writing-tests)
[![Psalm](https://img.shields.io/badge/Psalm-Level%201-brightgreen.svg?style=flat)](https://psalm.dev/docs/running_psalm/installation/)
[![CodeFactor](https://www.codefactor.io/repository/github/laurentmuller/fpdf2/badge)](https://www.codefactor.io/repository/github/laurentmuller/fpdf2)
[![Codecov](https://codecov.io/gh/laurentmuller/fpdf2/graph/badge.svg?token=16I8LCYRRS)](https://codecov.io/gh/laurentmuller/fpdf2)

## Actions

[![PHP-CS-Fixer](https://github.com/laurentmuller/fpdf2/actions/workflows/php-cs-fixer.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/php-cs-fixer.yaml)
[![PHPStan](https://github.com/laurentmuller/fpdf2/actions/workflows/php_stan.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/php_stan.yaml)
[![PHPUnit](https://github.com/laurentmuller/fpdf2/actions/workflows/php_unit.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/php_unit.yaml)
[![Psalm](https://github.com/laurentmuller/fpdf2/actions/workflows/pslam.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/pslam.yaml)
[![Rector](https://github.com/laurentmuller/fpdf2/actions/workflows/rector.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/rector.yaml)
[![Lint](https://github.com/laurentmuller/fpdf2/actions/workflows/lint.yaml/badge.svg)](https://github.com/laurentmuller/fpdf2/actions/workflows/lint.yaml)
[![StyleCI](https://github.styleci.io/repos/752676081/shield?branch=main)](https://github.styleci.io/repos/752676081?branch=main)
