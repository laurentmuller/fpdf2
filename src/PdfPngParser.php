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
 * Parser for PNG images.
 *
 * @phpstan-import-type ImageType from PdfDocument
 */
class PdfPngParser implements PdfImageParserInterface
{
    private string $signature = '';

    /**
     * @phpstan-return ImageType
     */
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
    protected function parseStream(PdfDocument $parent, $stream, string $file): array
    {
        // check header signature
        $this->checkSignature($stream, $file);

        // read header chunk
        $this->checkHeader($stream, $file);

        // get first block values
        $width = $this->readInt($stream);
        $height = $this->readInt($stream);
        $bitsPerComponent = $this->getBitsPerComponent($stream, $file);
        $colorType = $this->readByte($stream);

        $colorSpace = match ($colorType) {
            0, 4 => 'DeviceGray',
            2, 6 => 'DeviceRGB',
            3 => 'Indexed',
            default => throw PdfException::format('Color type %d not supported: %s.', $colorType, $file),
        };

        // check other values
        $this->checkCompression($stream, $file);
        $this->checkFilter($stream, $file);
        $this->checkInterlacing($stream, $file);
        $this->readStream($stream, 4);

        $decodeParams = \sprintf(
            '/Predictor 15 /Colors %d /BitsPerComponent %d /Columns %d',
            'DeviceRGB' === $colorSpace ? 3 : 1,
            $bitsPerComponent,
            $width
        );

        // scan chunks looking for the palette, transparency and image data
        $data = '';
        $palette = '';
        $transparencies = [];
        do {
            $length = $this->readInt($stream);
            /** @phpstan-var 'PLTE'|'tRNS'|'IDAT'|'IEND' $type */
            $type = $this->readStream($stream, 4);
            switch ($type) {
                case 'PLTE': // palette
                    $palette = $this->getPalette($stream, $length);
                    $this->readStream($stream, 4);
                    break;
                case 'tRNS': // transparency
                    $transparencies = $this->getTransparencies($stream, $length, $colorType);
                    $this->readStream($stream, 4);
                    break;
                case 'IDAT': // image data
                    $data .= $this->readStream($stream, $length);
                    $this->readStream($stream, 4);
                    break;
                default: // skip
                    $this->readStream($stream, $length + 4);
                    break;
            }
        } while ($length);

        if ('Indexed' === $colorSpace && '' === $palette) {
            throw PdfException::format('Missing palette: %s.', $file);
        }

        $image = [
            'index' => 0,
            'number' => 0,
            'width' => $width,
            'height' => $height,
            'color_space' => $colorSpace,
            'bits_per_component' => $bitsPerComponent,
            'filter' => 'FlateDecode',
            'decode_parms' => $decodeParams,
            'palette' => $palette,
            'transparencies' => $transparencies,
        ];

        if ($colorType >= 4) {
            // extract alpha channel
            [$data, $soft_mask] = $this->extractAlphaChannel($width, $height, $colorType, $data);
            $image['soft_mask'] = $soft_mask;
            $parent->setWithAlpha(true);
            $parent->updatePdfVersion('1.4');
        }
        $image['data'] = $data;

        return $image;
    }

    /**
     * Check if the compression is set to 0.
     *
     * @param resource $stream
     *
     * @throws PdfException if the signature is invalid
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
     * @throws PdfException if the signature is invalid
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
     * @throws PdfException if the signature is invalid
     */
    private function checkHeader(mixed $stream, string $file): void
    {
        $this->readStream($stream, 4);
        if ('IHDR' !== $this->readStream($stream, 4)) {
            throw PdfException::format('Incorrect PNG header chunk (IHDR): %s.', $file);
        }
    }

    /**
     * Check if the interlacing is set to 0.
     *
     * @param resource $stream
     *
     * @throws PdfException if the signature is invalid
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
        if ('' === $this->signature) {
            $this->signature = \chr(0x89) . \chr(0x50) . \chr(0x4E) . \chr(0x47)
                . \chr(0x0D) . \chr(0x0A) . \chr(0x1A) . \chr(0x0A);
        }
        if ($this->readStream($stream, \strlen($this->signature)) !== $this->signature) {
            throw PdfException::format('Incorrect PNG header signature: %s.', $file);
        }
    }

    /**
     * @phpstan-return array{0: string, 1: string}
     */
    private function extractAlphaChannel(int $width, int $height, int $colorType, string $data): array
    {
        $isAlpha = 4 === $colorType;
        $len = $isAlpha ? 2 * $width : 4 * $width;
        $pattern1 = $isAlpha ? '/(.)./s' : '/(.{3})./s';
        $pattern2 = $isAlpha ? '/.(.)/s' : '/.{3}(.)/s';

        $color = '';
        $alpha = '';
        $data = (string) \gzuncompress($data);
        for ($i = 0; $i < $height; ++$i) {
            $pos = (1 + $len) * $i;
            $color .= $data[$pos];
            $alpha .= $data[$pos];
            $line = \substr($data, $pos + 1, $len);
            $color .= (string) \preg_replace($pattern1, '$1', $line);
            $alpha .= (string) \preg_replace($pattern2, '$1', $line);
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
     * @phpstan-param resource $stream
     * @phpstan-param non-negative-int $length
     */
    private function getPalette($stream, int $length): string
    {
        return $this->readStream($stream, $length);
    }

    /**
     * @phpstan-param resource $stream
     * @phpstan-param non-negative-int $length
     *
     * @phpstan-return int[]
     */
    private function getTransparencies($stream, int $length, int $colorType): array
    {
        $transparency = $this->readStream($stream, $length);
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
     */
    private function openStream(string $file)
    {
        $stream = \fopen($file, 'r');
        if (!\is_resource($stream)) {
            throw PdfException::format('Can not open image file: %s.', $file);
        }

        return $stream;
    }

    /**
     * Read a single character and convert to a value between 0 and 255.
     *
     * @param resource $stream the stream to read value from
     *
     * @throws PdfException if the stream is closed or if the end of the stream is reached
     *
     * @phpstan-return int<0, 255>
     */
    private function readByte($stream): int
    {
        return \ord($this->readStream($stream, 1));
    }

    /**
     * Read a 4-byte integer from the given stream.
     *
     * @param resource $stream the stream to read integer from
     *
     * @throws PdfException if the stream is closed or if the end of the stream is reached
     *
     * @phpstan-return non-negative-int
     */
    private function readInt($stream): int
    {
        /** @phpstan-var array{i: non-negative-int} $unpack */
        $unpack = \unpack('Ni', $this->readStream($stream, 4));

        return $unpack['i'];
    }

    /**
     * Read a string from the given stream.
     *
     * @param resource $stream the stream to read string from
     * @param int      $len    the number of bytes read
     *
     * @throws PdfException if the stream is closed or if the end of the stream is reached
     *
     * @phpstan-param resource|closed-resource $stream
     * @phpstan-param non-negative-int $len
     */
    private function readStream($stream, int $len): string
    {
        // @phpstan-ignore function.alreadyNarrowedType
        if (!\is_resource($stream)) {
            throw PdfException::instance('The stream is closed.');
        }

        // @phpstan-ignore argument.type
        $result = \fread($stream, $len);
        if (!\is_string($result) || $len !== \strlen($result)) {
            throw PdfException::instance('Unexpected end of stream.');
        }

        return $result;
    }
}
