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

use fpdf\PdfDocument;
use fpdf\PdfException;

/**
 * Abstract image parser that converts a file to a GD Image.
 */
abstract class AbstractGdImageParser extends PdfPngParser
{
    public function parse(PdfDocument $parent, string $file): array
    {
        $image = $this->createImageFromFile($file);
        if (!$image instanceof \GdImage) {
            throw PdfException::format('Missing or incorrect image file: %s.', $file);
        }

        $data = $this->toPngImage($image);
        $stream = $this->openDataStream($data);

        try {
            return $this->parseStream($parent, $stream, $file);
        } finally {
            $this->closeStream($stream);
            \imagedestroy($image);
        }
    }

    /**
     * Creates a GD image from the given file.
     *
     * @param string $file the image file
     *
     * @return \GdImage|false an image resource identifier on success, false on errors
     */
    abstract protected function createImageFromFile(string $file): \GdImage|false;

    protected function toPngImage(\GdImage $image): string
    {
        \ob_start();
        \imagepng($image);

        return (string) \ob_get_clean();
    }

    /**
     * @return resource
     */
    private function openDataStream(string $data)
    {
        /** @phpstan-var resource $stream */
        $stream = \fopen('php://temp', 'rb+');
        \fwrite($stream, $data);
        \rewind($stream);

        return $stream;
    }
}
