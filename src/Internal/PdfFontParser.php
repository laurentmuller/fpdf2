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

namespace fpdf\Internal;

use fpdf\PdfException;

/**
 * Class to parse JSON font files.
 *
 * @internal
 *
 * @phpstan-type FontType = array{
 *     name?: non-empty-string,
 *     type: string,
 *     file?: string,
 *     cw: array<int, int>,
 *     desc?: array<string, string>,
 *     diff?: string,
 *     enc?: string,
 *     length1?: int,
 *     length2?: int,
 *     originalsize?: int,
 *     size1?: int,
 *     size2?: int,
 *     subsetted?: bool,
 *     up: int,
 *     ut: int,
 *     uv?: array<int, int|int[]>}
 */
final class PdfFontParser
{
    /**
     * Create a PDF font from the given JSON file path.
     *
     * @throws PdfException if the given path does not exist, or if the font name is not defined
     */
    public function parse(string $path): PdfFont
    {
        if (!\file_exists($path)) {
            throw PdfException::format('Unable to find the font file: %s.', $path);
        }

        $data = $this->decodeJson($path);
        /** @phpstan-var non-empty-string|null $name */
        $name = $this->getString($data, 'name');
        if (null === $name) {
            throw PdfException::format('No font name defined in file: %s.', $path);
        }

        return new PdfFont(
            // common properties
            name: $name,
            type: $data['type'],
            file: $this->getString($data, 'file'),
            encoding: $this->getEncoding($data),
            cw: $data['cw'],
            up: $this->getInt($data, 'up'),
            ut: $this->getInt($data, 'ut'),
            uv: $data['uv'] ?? null,
            // generate font properties
            desc: $data['desc'] ?? [],
            diff: $this->getString($data, 'diff'),
            length1: $this->getInt($data, 'length1'),
            length2: $this->getInt($data, 'length2'),
            originalsize: $this->getInt($data, 'originalsize'),
            size1: $this->getInt($data, 'size1'),
            size2: $this->getInt($data, 'size2'),
            subsetted: $data['subsetted'] ?? false,
        );
    }

    /**
     * @phpstan-return FontType
     */
    private function decodeJson(string $path): array
    {
        try {
            $content = (string) \file_get_contents($path);

            /** @var FontType */
            return \json_decode(json: $content, associative: true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw PdfException::instance(\sprintf('Unable to parse the font file: %s.', $path), $e);
        }
    }

    /**
     * @phpstan-param FontType $data
     */
    private function getEncoding(array $data): ?string
    {
        $enc = $this->getString($data, 'enc');

        return null === $enc ? null : \strtolower($enc);
    }

    /**
     * @phpstan-param FontType $data
     */
    private function getInt(array $data, string $key): int
    {
        if (isset($data[$key]) && \is_int($data[$key])) {
            return $data[$key];
        }

        return 0;
    }

    /**
     * @phpstan-param FontType $data
     */
    private function getString(array $data, string $key): ?string
    {
        if (isset($data[$key]) && \is_string($data[$key]) && '' !== $data[$key]) {
            return $data[$key];
        }

        return null;
    }
}
