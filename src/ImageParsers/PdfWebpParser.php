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

namespace fpdf\ImageParsers;

/**
 * Parser for Webp images.
 */
class PdfWebpParser extends AbstractGdImageParser
{
    protected function createImageFromFile(string $file): \GdImage|false
    {
        return \imagecreatefromwebp($file);
    }
}
