<?php

/*
 * This file is part of the FPDF2 package.
 *
 * (c) bibi.nu <bibi@bibi.nu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace fpdf\Tests;

use fpdf\Enums\PdfDestination;

final class PdfDocFontCompressedTest extends AbstractPdfDocTestCase
{
    public function testAddFont(): void
    {
        $name = 'Comic';
        $file = 'ComicNeue-Regular.json';
        $dir = __DIR__ . '/fonts';
        $doc = $this->createDocument();
        $doc->addFont(
            family: $name,
            file: $file,
            dir: $dir
        );
        $doc->setFont($name);
        $doc->output(PdfDestination::STRING);
        self::assertSame(1, $doc->getPage());
    }
}
