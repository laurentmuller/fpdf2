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

namespace fpdf\ImageParsers;

/**
 * Parser for Bitmap images.
 */
class PdfBmpParser extends AbstractGdImageParser
{
    #[\Override]
    protected function createImageFromFile(string $file): \GdImage|false
    {
        return \imagecreatefrombmp($file);
    }
}
