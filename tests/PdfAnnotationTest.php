<?php

/*
 * This file is part of the 'fpdf' package.
 *
 * For the license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author bibi.nu <bibi@bibi.nu>
 */

declare(strict_types=1);

namespace fpdf\Tests;

use fpdf\Color\PdfCmykColor;
use fpdf\Color\PdfRgbColor;
use fpdf\Enums\PdfAnnotationName;
use fpdf\Enums\PdfDestination;

class PdfAnnotationTest extends AbstractPdfDocTestCase
{
    public function testAnnotation(): void
    {
        $doc = $this->createDocument()
            ->setCompression(false);

        $y = 0.0;
        $top = $doc->getTopMargin();
        $left = $doc->getLeftMargin() + 5.0;
        $names = PdfAnnotationName::cases();
        $colors = [
            PdfRgbColor::red(),
            PdfRgbColor::green(),
            PdfRgbColor::blue(),
            PdfCmykColor::yellow(),
            PdfCmykColor::magenta(),
            PdfCmykColor::cyan(),
        ];

        foreach ($names as $name) {
            $text = \sprintf('Annotation with "%s" icon', $name->value);
            $color = $colors[\array_rand($colors)];
            $doc->annotation(
                text: $text,
                x: 0,
                y: $y,
                width: 18,
                height: 18,
                title: \sprintf('%s Title', $name->value),
                name: $name,
                color: $color,
            );
            $doc->text($left, $top + $y, $text);
            $y += 10.0;
        }
        $content = $doc->output(PdfDestination::STRING);
        self::assertStringContainsString('<</Type /Annot /Subtype /Text /Rect', $content);
    }
}
