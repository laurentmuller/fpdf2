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

use fpdf\Enums\PdfDestination;

class PdfDocFontCompressedTest extends AbstractPdfDocTestCase
{
    public function testAddFont(): void
    {
        $name = 'Comic';
        $file = 'ComicNeue-Regular.php';
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
