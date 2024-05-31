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

namespace fpdf;

/**
 * Parser for GIF images.
 *
 * @phpstan-import-type ImageType from PdfDocument
 */
class PdfGifParser extends PdfPngParser
{
    public function parse(PdfDocument $parent, string $file): array
    {
        $image = \imagecreatefromgif($file);
        if (!$image instanceof \GdImage) {
            throw PdfException::format('Missing or incorrect image file: %s.', $file);
        }

        $data = $this->toPngImage($image);
        $stream = $this->openStream($data);

        try {
            return $this->parseStream($parent, $stream, $file);
        } finally {
            $this->closeStream($stream);
            \imagedestroy($image);
        }
    }

    /**
     * @return resource
     *
     * @throws PdfException if the stream cannot be open
     */
    private function openStream(string $data)
    {
        $stream = \fopen('php://temp', 'rb+');
        if (!\is_resource($stream)) {
            throw PdfException::instance('Unable to create memory stream.');
        }

        \fwrite($stream, $data);
        \rewind($stream);

        return $stream;
    }

    private function toPngImage(\GdImage $image): string
    {
        \imageinterlace($image, false);
        \ob_start();
        \imagepng($image);

        return (string) \ob_get_clean();
    }
}
