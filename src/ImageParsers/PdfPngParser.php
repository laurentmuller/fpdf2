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

use fpdf\Enums\PdfColorSpace;
use fpdf\Enums\PdfVersion;
use fpdf\Interfaces\PdfImageParserInterface;
use fpdf\Internal\PdfImage;
use fpdf\PdfDocument;
use fpdf\PdfException;

/**
 * Parser for PNG images.
 *
 * @see https://en.wikipedia.org/wiki/PNG  Documentation for PNG format.
 */
class PdfPngParser implements PdfImageParserInterface
{
    /** The image data chunk type. */
    private const string CHUNK_DATA = 'IDAT';
    /** The image end chunk type. */
    private const string CHUNK_END = 'IEND';
    /** The header chunk type. */
    private const string CHUNK_HEADER = 'IHDR';
    /** The palette chunk type. */
    private const string CHUNK_PALETTE = 'PLTE';
    /** The transparency chunk type. */
    private const string CHUNK_TRANSPARENCY = 'tRNS';
    /** The PNG file signature. */
    private const string FILE_SIGNATURE = "\x89PNG\x0d\x0a\x1a\x0a";
    /** The header chunk length. */
    private const int HEADER_LENGTH = 13;

    #[\Override]
    public function parse(PdfDocument $parent, string $file): PdfImage
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
     * @param resource $stream the stream to read from
     *
     * @throws PdfException if an error occurs while reading the stream
     */
    protected function parseStream(PdfDocument $parent, mixed $stream, string $file): PdfImage
    {
        // check signature
        $this->checkSignature($stream, $file);

        // parse header chunk
        $data = $this->parseHeader($stream, $file);

        // get header values
        $width = $this->getInt($data, 0);
        $height = $this->getInt($data, 4);
        $bitsPerComponent = $this->getBitsPerComponent($data[8], $file);
        $colorType = $this->getByte($data, 9);
        $colorSpace = $this->getColorSpace($colorType, $file);
        $colors = $this->getColors($colorSpace);

        // check other values
        $this->checkCompression($data[10], $file);
        $this->checkFilter($data[11], $file);
        $this->checkInterlacing($data[12], $file);

        // scan chunks looking for the palette, transparency and image data
        $data = '';
        $palette = '';
        $transparencies = [];

        do {
            [$length, $type, $content] = $this->readChunk($stream);
            switch ($type) {
                case self::CHUNK_PALETTE:
                    $palette = $content;
                    break;
                case self::CHUNK_TRANSPARENCY:
                    $transparencies = $this->getTransparencies($content, $colorType);
                    break;
                case self::CHUNK_DATA:
                    $data .= $content;
                    break;
                case self::CHUNK_END:
                    $length = 0;
                    break;
            }
        } while ($length);

        // check palette
        $this->checkPalette($colorSpace, $palette, $file);

        // the decoded parameters
        $decodeParms = \sprintf(
            '/Predictor 15 /Colors %d /BitsPerComponent %d /Columns %d',
            $colors,
            $bitsPerComponent,
            $width
        );

        $image = new PdfImage(
            width: $width,
            height: $height,
            colorSpace: $colorSpace,
            bitsPerComponent: $bitsPerComponent,
            data: $data,
            filter: 'FlateDecode',
            decodeParms: $decodeParms,
            palette: $palette,
            transparencies: $transparencies
        );

        if ($colorType >= 4) {
            [$data, $softMask] = $this->extractAlphaChannel($width, $height, $colorType, $data);
            $parent->updatePdfVersion(PdfVersion::VERSION_1_4)
                ->setAlphaChannel(true);
            $image->softMask = $softMask;
            $image->data = $data;
        }

        return $image;
    }

    /**
     * Check if the compression is set to 0.
     *
     * @param string $data the single character from the header ('IHDR') chunk data
     *
     * @throws PdfException if the compression method is not supported
     */
    private function checkCompression(string $data, string $file): void
    {
        $value = $this->getByte($data);
        if (0 !== $value) {
            throw PdfException::format('Compression method %d not supported: %s.', $value, $file);
        }
    }

    /**
     * Check if the filter is set to 0.
     *
     * @param string $data the single character from the header ('IHDR') chunk data
     *
     * @throws PdfException if the filter method is not supported
     */
    private function checkFilter(string $data, string $file): void
    {
        $value = $this->getByte($data);
        if (0 !== $value) {
            throw PdfException::format('Filter method %d not supported: %s.', $value, $file);
        }
    }

    /**
     * Check if the interlacing is set to 0.
     *
     * @param string $data the single character from the header ('IHDR') chunk data
     *
     * @throws PdfException if the interlacing is not supported
     */
    private function checkInterlacing(string $data, string $file): void
    {
        $value = $this->getByte($data);
        if (0 !== $value) {
            throw PdfException::format('Interlacing %d not supported: %s.', $value, $file);
        }
    }

    /**
     * Check if the palette is valid.
     *
     * @throws PdfException if the palette is invalid
     */
    private function checkPalette(PdfColorSpace $colorSpace, string $palette, string $file): void
    {
        if (PdfColorSpace::INDEXED === $colorSpace && '' === $palette) {
            throw PdfException::format('Missing palette: %s.', $file);
        }
    }

    /**
     * Check the PNG file signature.
     *
     * @param resource $stream the stream to read signature from
     *
     * @throws PdfException if the signature is invalid
     */
    private function checkSignature(mixed $stream, string $file): void
    {
        if (self::FILE_SIGNATURE !== $this->readString($stream, \strlen(self::FILE_SIGNATURE))) {
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
            $color .= \preg_replace($colorPattern, '$1', $line);
            $alpha .= \preg_replace($alphaPattern, '$1', $line);
        }
        $data = (string) \gzcompress($color);
        $mask = (string) \gzcompress($alpha);

        return [$data, $mask];
    }

    /**
     * Gets bits per component.
     *
     * @param string $data the header ('IHDR') chunk data
     *
     * @throws PdfException if the bits per component are greater than 8
     */
    private function getBitsPerComponent(string $data, string $file): int
    {
        $bcp = $this->getByte($data);
        if ($bcp > 8) {
            throw PdfException::format('Bits per component %d not supported: %s.', $bcp, $file);
        }

        return $bcp;
    }

    /**
     * Gets a single character and convert to a value between 0 and 255.
     *
     * @param string $data   the data to get character from
     * @param int    $offset the offset to start reading from
     */
    private function getByte(string $data, int $offset = 0): int
    {
        return \ord($data[$offset]);
    }

    /**
     * Gets the number of colors.
     */
    private function getColors(PdfColorSpace $colorSpace): int
    {
        return PdfColorSpace::DEVICE_RGB === $colorSpace ? 3 : 1;
    }

    /**
     * Gets the color space.
     *
     * @throws PdfException if the color type is not supported
     */
    private function getColorSpace(int $colorType, string $file): PdfColorSpace
    {
        return match ($colorType) {
            0, 4 => PdfColorSpace::DEVICE_GRAY,
            2, 6 => PdfColorSpace::DEVICE_RGB,
            3 => PdfColorSpace::INDEXED,
            default => throw PdfException::format('Color type %d not supported: %s.', $colorType, $file),
        };
    }

    /**
     * Gets a 4-byte unsigned long, big endian, from the given string.
     *
     * @param string $data   the data to get integer from
     * @param int    $offset the offset to start reading from
     */
    private function getInt(string $data, int $offset): int
    {
        return $this->unpack(\substr($data, $offset, 4));
    }

    /**
     * Gets the transparencies.
     *
     * @param string $data the chunk data
     *
     * @return int[] the transparencies
     */
    private function getTransparencies(string $data, int $colorType): array
    {
        switch ($colorType) {
            case 0:
                return [$this->getByte($data, 1)];
            case 2:
                return [
                    $this->getByte($data, 1),
                    $this->getByte($data, 3),
                    $this->getByte($data, 5),
                ];
            default:
                $pos = \strpos($data, \chr(0));

                return false !== $pos ? [$pos] : [];
        }
    }

    /**
     * Open the given file.
     *
     * @return resource a file pointer resource
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
     * Parse and check the first block header ('IHDR').
     *
     * @param resource $stream the stream to read chunk from
     *
     * @return string the chunk data
     *
     * @throws PdfException if the header is invalid
     */
    private function parseHeader(mixed $stream, string $file): string
    {
        [$length, $type, $data] = $this->readChunk($stream);
        if (self::HEADER_LENGTH !== $length) {
            throw PdfException::format('Incorrect PNG header length (%d): %s.', $length, $file);
        }
        if (self::CHUNK_HEADER !== $type) {
            throw PdfException::format('Incorrect PNG header chunk (%s): %s.', $type, $file);
        }

        return $data;
    }

    /**
     * Reads a PNG chunk from the given stream.
     *
     * @param resource $stream the stream to read chunk from
     *
     * @return array{0: int, 1: string, 2: string} the chunk length, type, and data
     *
     * @throws PdfException if the end of the stream is reached
     */
    private function readChunk(mixed $stream): array
    {
        $length = $this->readInt($stream);
        $type = $this->readString($stream, 4);
        $data = $length > 0 ? $this->readString($stream, $length) : '';
        $this->readString($stream, 4); // CRC

        return [$length, $type, $data];
    }

    /**
     * Read a 4-byte unsigned long, big endian, from the given stream.
     *
     * @param resource $stream the stream to read integer from
     *
     * @throws PdfException if the end of the stream is reached
     */
    private function readInt(mixed $stream): int
    {
        return $this->unpack($this->readString($stream, 4));
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
     * Convert the given data to a 4-byte unsigned long, big endian.
     *
     * @param string $data the data to get integer from
     */
    private function unpack(string $data): int
    {
        /** @phpstan-var int */
        return \unpack('N', $data)[1];
    }
}
