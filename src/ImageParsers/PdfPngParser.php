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

use fpdf\Enums\PdfVersion;
use fpdf\Interfaces\PdfImageParserInterface;
use fpdf\PdfDocument;
use fpdf\PdfException;

/**
 * Parser for PNG images.
 *
 * @phpstan-import-type ImageType from PdfDocument
 */
class PdfPngParser implements PdfImageParserInterface
{
    /**
     * @phpstan-return ImageType
     */
    #[\Override]
    public function parse(PdfDocument $parent, string $file): array
    {
        $stream = $this->openStream($file);

        try {
            return $this->parseStream($parent, $stream, $file);
        } finally {
            $this->closeStream($stream);
        }
    }

    /**
     * Close the given stream.
     *
     * @param ?resource $stream
     */
    protected function closeStream(mixed $stream): void
    {
        if (\is_resource($stream)) {
            \fclose($stream);
        }
    }

    /**
     * Parse the given PNG stream.
     *
     * @param resource $stream
     *
     * @throws PdfException if an error occurs while reading the stream
     *
     * @phpstan-return ImageType
     */
    protected function parseStream(PdfDocument $parent, mixed $stream, string $file): array
    {
        // check signature
        $this->checkSignature($stream, $file);

        // check header chunk
        $this->checkHeader($stream, $file);

        // get header values
        $width = $this->readInt($stream);
        $height = $this->readInt($stream);
        $bitsPerComponent = $this->getBitsPerComponent($stream, $file);
        $colorType = $this->readByte($stream);
        $colorSpace = $this->getColorSpace($colorType, $file);
        $colors = $this->getColors($colorSpace);

        // check other values
        $this->checkCompression($stream, $file);
        $this->checkFilter($stream, $file);
        $this->checkInterlacing($stream, $file);
        $this->skip($stream, 4); // CRC

        $decodeParms = \sprintf(
            '/Predictor 15 /Colors %d /BitsPerComponent %d /Columns %d',
            $colors,
            $bitsPerComponent,
            $width
        );

        // scan chunks looking for the palette, transparency and image data
        $data = '';
        $palette = '';
        $transparencies = [];
        do {
            /** @var positive-int $length */
            $length = $this->readInt($stream);
            $type = $this->readString($stream, 4);
            switch ($type) {
                case 'PLTE': // palette
                    $palette = $this->getPalette($stream, $length);
                    break;
                case 'tRNS': // transparency
                    $transparencies = $this->getTransparencies($stream, $length, $colorType);
                    break;
                case 'IDAT': // image data
                    $data .= $this->readString($stream, $length);
                    break;
                case 'IEND': // image end
                    $length = 0;
                    break;
                default: // skip other content
                    $this->skip($stream, $length);
                    break;
            }
            $this->skip($stream, 4); // CRC
        } while ($length);

        if ('Indexed' === $colorSpace && '' === $palette) {
            throw PdfException::format('Missing palette: %s.', $file);
        }

        $image = [
            'index' => 0,
            'number' => 0,
            'width' => $width,
            'height' => $height,
            'colorSpace' => $colorSpace,
            'bitsPerComponent' => $bitsPerComponent,
            'filter' => 'FlateDecode',
            'decodeParms' => $decodeParms,
            'palette' => $palette,
            'transparencies' => $transparencies,
            'data' => $data,
        ];

        if ($colorType >= 4) {
            // extract alpha channel
            [$data, $softMask] = $this->extractAlphaChannel($width, $height, $colorType, $data);
            $parent->updatePdfVersion(PdfVersion::VERSION_1_4);
            $parent->setAlphaChannel(true);
            $image['softMask'] = $softMask;
            $image['data'] = $data;
        }

        return $image;
    }

    /**
     * Check if the compression is set to 0.
     *
     * @param resource $stream
     *
     * @throws PdfException if the compression method is not supported
     */
    private function checkCompression(mixed $stream, string $file): void
    {
        $value = $this->readByte($stream);
        if (0 !== $value) {
            throw PdfException::format('Compression method %d not supported: %s.', $value, $file);
        }
    }

    /**
     * Check if the filter is set to 0.
     *
     * @param resource $stream
     *
     * @throws PdfException if the filter method is not supported
     */
    private function checkFilter(mixed $stream, string $file): void
    {
        $value = $this->readByte($stream);
        if (0 !== $value) {
            throw PdfException::format('Filter method %d not supported: %s.', $value, $file);
        }
    }

    /**
     * Check the first block header.
     *
     * @param resource $stream
     *
     * @throws PdfException if the header is invalid
     */
    private function checkHeader(mixed $stream, string $file): void
    {
        $size = $this->readInt($stream);
        if (13 !== $size) {
            throw PdfException::format('Incorrect PNG header length (%d): %s.', $size, $file);
        }
        $header = $this->readString($stream, 4);
        if ('IHDR' !== $header) {
            throw PdfException::format('Incorrect PNG header chunk (%s): %s.', $header, $file);
        }
    }

    /**
     * Check if the interlacing is set to 0.
     *
     * @param resource $stream
     *
     * @throws PdfException if the interlacing is not supported
     */
    private function checkInterlacing(mixed $stream, string $file): void
    {
        $value = $this->readByte($stream);
        if (0 !== $value) {
            throw PdfException::format('Interlacing %d not supported: %s.', $value, $file);
        }
    }

    /**
     * Check PNG header signature.
     *
     * @param resource $stream
     *
     * @throws PdfException if the signature is invalid
     */
    private function checkSignature(mixed $stream, string $file): void
    {
        static $signature = '';
        if ('' === $signature) {
            $signature = \chr(0x89) . \chr(0x50) . \chr(0x4E) . \chr(0x47)
                . \chr(0x0D) . \chr(0x0A) . \chr(0x1A) . \chr(0x0A);
        }

        /** @phpstan-var non-empty-string $signature */
        if ($this->readString($stream, \strlen($signature)) !== $signature) {
            throw PdfException::format('Incorrect PNG header signature: %s.', $file);
        }
    }

    /**
     * Extract the alpha channel.
     *
     * @return array{0: string, 1: string}
     */
    private function extractAlphaChannel(int $width, int $height, int $colorType, string $data): array
    {
        $isAlpha = 4 === $colorType;
        $len = $isAlpha ? 2 * $width : 4 * $width;
        $colorPattern = $isAlpha ? '/(.)./s' : '/(.{3})./s';
        $alphaPattern = $isAlpha ? '/.(.)/s' : '/.{3}(.)/s';

        $color = '';
        $alpha = '';
        $data = (string) \gzuncompress($data);
        for ($i = 0; $i < $height; ++$i) {
            $pos = (1 + $len) * $i;
            $color .= $data[$pos];
            $alpha .= $data[$pos];
            $line = \substr($data, $pos + 1, $len);
            $color .= (string) \preg_replace($colorPattern, '$1', $line);
            $alpha .= (string) \preg_replace($alphaPattern, '$1', $line);
        }
        $data = (string) \gzcompress($color);
        $mask = (string) \gzcompress($alpha);

        return [$data, $mask];
    }

    /**
     * Gets bits per component.
     *
     * @param resource $stream
     *
     * @throws PdfException if the bits per component are greater than 8
     */
    private function getBitsPerComponent(mixed $stream, string $file): int
    {
        $bcp = $this->readByte($stream);
        if ($bcp > 8) {
            throw PdfException::format('Bits per component %d not supported: %s.', $bcp, $file);
        }

        return $bcp;
    }

    /**
     * Gets the number of colors.
     */
    private function getColors(string $colorSpace): int
    {
        return 'DeviceRGB' === $colorSpace ? 3 : 1;
    }

    /**
     * Gets the color space.
     *
     * @throws PdfException if the color type is not supported
     */
    private function getColorSpace(int $colorType, string $file): string
    {
        return match ($colorType) {
            0, 4 => 'DeviceGray',
            2, 6 => 'DeviceRGB',
            3 => 'Indexed',
            default => throw PdfException::format('Color type %d not supported: %s.', $colorType, $file),
        };
    }

    /**
     * @param resource     $stream
     * @param positive-int $length
     *
     * @throws PdfException if the end of the stream is reached
     */
    private function getPalette(mixed $stream, int $length): string
    {
        return $this->readString($stream, $length);
    }

    /**
     * @param resource     $stream
     * @param positive-int $length
     *
     * @return int[]
     *
     * @throws PdfException if the end of the stream is reached
     */
    private function getTransparencies(mixed $stream, int $length, int $colorType): array
    {
        $transparency = $this->readString($stream, $length);
        switch ($colorType) {
            case 0:
                return [\ord(\substr($transparency, 1, 1))];
            case 2:
                return [
                    \ord(\substr($transparency, 1, 1)),
                    \ord(\substr($transparency, 3, 1)),
                    \ord(\substr($transparency, 5, 1)),
                ];
            default:
                $pos = \strpos($transparency, \chr(0));

                return false !== $pos ? [$pos] : [];
        }
    }

    /**
     * @return resource
     *
     * @throws PdfException if the image file cannot be open
     */
    private function openStream(string $file): mixed
    {
        $stream = \fopen($file, 'r');
        if (!\is_resource($stream)) {
            throw PdfException::format('Unable to open image file: %s.', $file);
        }

        return $stream;
    }

    /**
     * Read a single character and convert to a value between 0 and 255.
     *
     * @param resource $stream the stream to read value from
     *
     * @throws PdfException if the end of the stream is reached
     */
    private function readByte(mixed $stream): int
    {
        return \ord($this->readString($stream, 1));
    }

    /**
     * Read a 4-byte integer from the given stream.
     *
     * @param resource $stream the stream to read integer from
     *
     * @throws PdfException if the end of the stream is reached
     */
    private function readInt(mixed $stream): int
    {
        /** @var array{i: int} $unpack */
        $unpack = \unpack('Ni', $this->readString($stream, 4));

        return $unpack['i'];
    }

    /**
     * Read a string from the given stream.
     *
     * @param resource     $stream the stream to read string from
     * @param positive-int $length the number of bytes to read
     *
     * @throws PdfException if the end of the stream is reached
     */
    private function readString(mixed $stream, int $length): string
    {
        $result = \fread($stream, $length);
        if (!\is_string($result) || $length !== \strlen($result)) {
            throw PdfException::instance('Unexpected end of stream.');
        }

        return $result;
    }

    /**
     * Skip the given number of bytes. Do nothing if the given length is smaller than or equal to 0.
     *
     * @param resource $stream the stream to skip from
     * @param int      $length the number of bytes to skip
     *
     * @throws PdfException if the end of the stream is reached
     */
    private function skip(mixed $stream, int $length): void
    {
        if ($length > 0) {
            $this->readString($stream, $length);
        }
    }
}
