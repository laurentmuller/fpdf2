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

namespace fpdf\Tests\ImageParsers;

use fpdf\Interfaces\PdfImageParserInterface;
use fpdf\Internal\PdfImage;
use fpdf\PdfDocument;
use PHPUnit\Framework\TestCase;

abstract class AbstractPdfParserTestCase extends TestCase
{
    abstract protected function createParser(): PdfImageParserInterface;

    protected function parseFile(string $file): PdfImage
    {
        $parent = new PdfDocument();
        $parser = $this->createParser();
        $path = __DIR__ . '/../images/' . $file;

        return $parser->parse($parent, $path);
    }
}
