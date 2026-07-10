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

namespace fpdf\Internal;

use fpdf\Enums\PdfFontName;
use fpdf\Enums\PdfFontType;
use fpdf\PdfWriter;

/**
 * Class to write font files and fonts.
 */
readonly class PdfFontWriter
{
    public function __construct(private PdfWriter $writer)
    {
    }

    /**
     * Put a font to this writer.
     *
     * This function must be called after writing the font file.
     *
     * @param array<string, PdfFontFile> $fontFiles
     * @param array<string, int>         $encodings
     * @param array<string, int>         $charMaps
     */
    public function putFont(PdfFont $font, array $fontFiles, array &$encodings, array &$charMaps): void
    {
        // encoding
        if ($font->isDiff()) {
            $encoding = $font->encoding ?? '';
            if (!isset($encodings[$encoding])) {
                $this->writer->putNewObj();
                $this->writer->putf('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences [%s]>>', $font->diff);
                $this->writer->putEndObj();
                $encodings[$encoding] = $this->writer->getObjectNumber();
            }
        }
        // ToUnicode CMap
        $mapKey = '';
        $name = $font->name;
        if ($font->isUv()) {
            $mapKey = $font->encoding ?? $name;
            if (!isset($charMaps[$mapKey])) {
                $map = $this->toUnicodeCmap($font->uv);
                $this->writer->putStreamObject($map);
                $charMaps[$mapKey] = $this->writer->getObjectNumber();
            }
        }
        // font object
        $font->number = $this->writer->getObjectNumber() + 1;
        if ($font->subsetted) {
            $name = 'AAAAAA+' . $name;
        }
        switch ($font->type) {
            case PdfFontType::CORE:
                $this->putFontCore($name, $font, $mapKey, $charMaps);
                break;
            default:
                $this->putFontOther($name, $font, $mapKey, $charMaps, $fontFiles, $encodings);
                break;
        }
    }

    /**
     * Put a font file to this writer.
     *
     * This function must be called before writing the font.
     *
     * @param string      $file     the font file path
     * @param PdfFontFile $fontFile the font file info
     */
    public function putFontFile(string $file, PdfFontFile $fontFile): void
    {
        $this->writer->putNewObj();
        $fontFile->number = $this->writer->getObjectNumber();
        $content = (string) \file_get_contents($file);
        $compressed = \str_ends_with($file, '.z');
        $length1 = $fontFile->length1;
        $length2 = $fontFile->length2;
        if (!$compressed && $fontFile->isLength2()) {
            $content = \substr($content, 6, $length1) . \substr($content, 6 + $length1 + 6, $length2);
        }
        $this->writer->putf('<</Length %d', \strlen($content));
        if ($compressed) {
            $this->writer->put('/Filter /FlateDecode');
        }
        $this->writer->putf('/Length1 %d', $length1);
        if ($fontFile->isLength2()) {
            $this->writer->putf('/Length2 %d /Length3 0', $length2 ?? 0);
        }
        $this->writer->put('>>');
        $this->writer->putStream($content);
        $this->writer->putEndObj();
    }

    /**
     * @param array<string, PdfFont> $fonts
     */
    public function putResourceFonts(array $fonts): void
    {
        $this->writer->put('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
        $this->writer->put('/Font <<');
        foreach ($fonts as $font) {
            $this->writer->putf('/F%d %s', $font->index, $font->formatNumber());
        }
        $this->writer->put('>>');
    }

    /**
     * Write a font object of type Core.
     *
     * @param array<string, int> $charMaps
     */
    private function putFontCore(
        string $name,
        PdfFont $font,
        string $mapKey,
        array $charMaps
    ): void {
        $this->writer->putNewObj();
        $this->writer->put('<</Type /Font');
        $this->writer->putf('/BaseFont /%s', $name);
        $this->writer->put('/Subtype /Type1');
        if (!(PdfFontName::tryFrom($name)?->useRegular() ?? false)) {
            $this->writer->put('/Encoding /WinAnsiEncoding');
        }
        if ($font->isUv()) {
            $this->writer->putf('/ToUnicode %d 0 R', $charMaps[$mapKey]);
        }
        $this->writer->put('>>');
        $this->writer->putEndObj();
    }

    /**
     * Write a font object of TrueType or Type1.
     *
     * @param array<string, int>         $charMaps
     * @param array<string, PdfFontFile> $fontFiles
     * @param array<string, int>         $encodings
     */
    private function putFontOther(
        string $name,
        PdfFont $font,
        string $mapKey,
        array $charMaps,
        array $fontFiles,
        array $encodings
    ): void {
        $this->writer->putNewObj();
        $this->writer->put('<</Type /Font');
        $this->writer->putf('/BaseFont /%s', $name);
        $this->writer->putf('/Subtype /%s', $font->type);
        $this->writer->put('/FirstChar 32 /LastChar 255');
        $this->writer->putf('/Widths %d 0 R', $this->writer->getObjectNumber() + 1);
        $this->writer->putf('/FontDescriptor %d 0 R', $this->writer->getObjectNumber() + 2);
        if ($font->isEncoding() && $font->isDiff()) {
            $this->writer->putf('/Encoding %d 0 R', $encodings[$font->encoding]);
        } else {
            $this->writer->put('/Encoding /WinAnsiEncoding');
        }
        if ($font->isUv()) {
            $this->writer->putf('/ToUnicode %d 0 R', $charMaps[$mapKey]);
        }
        $this->writer->put('>>');
        $this->writer->putEndObj();
        // widths
        $this->writer->putNewObj();
        $this->writer->putf('[%s]', \implode(' ', \array_slice($font->cw, 32)));
        $this->writer->putEndObj();
        // descriptor
        $this->writer->putNewObj();
        $output = PdfWriter::sprintf('<</Type /FontDescriptor /FontName /%s', $name);
        foreach ($font->desc as $descKey => $descValue) {
            if (\is_array($descValue)) { // FontBBox
                $descValue = PdfWriter::sprintf('[%s]', \implode(' ', $descValue));
            }
            $output .= PdfWriter::sprintf(' /%s %s', $descKey, $descValue);
        }
        if ($font->isFile()) {
            $fontFile = $font->isType1() ? '' : '2';
            $number = $fontFiles[$font->file]->formatNumber();
            $output .= PdfWriter::sprintf(' /FontFile%s %s', $fontFile, $number);
        }
        $output .= '>>';
        $this->writer->put($output);
        $this->writer->putEndObj();
    }

    /**
     * Convert the V type array to the Unicode character map.
     *
     * @phpstan-param array<int, int|int[]> $uv
     */
    private function toUnicodeCmap(array $uv): string
    {
        $chars = [];
        $ranges = [];
        foreach ($uv as $c => $v) {
            if (\is_array($v)) {
                $ranges[] = PdfWriter::sprintf('<%02X> <%02X> <%04X>', $c, $c + $v[1] - 1, $v[0]);
            } else {
                $chars[] = PdfWriter::sprintf('<%02X> <%04X>', $c, $v);
            }
        }
        $output = [
            '/CIDInit /ProcSet findresource begin',
            '12 dict begin',
            'begincmap',
            '/CIDSystemInfo',
            '<</Registry (Adobe)',
            '/Ordering (UCS)',
            '/Supplement 0',
            '>> def',
            '/CMapName /Adobe-Identity-UCS def',
            '/CMapType 2 def',
            '1 begincodespacerange',
            '<00> <FF>',
            'endcodespacerange',
        ];
        if ([] !== $ranges) {
            $output[] = PdfWriter::sprintf('%d beginbfrange', \count($ranges));
            $output[] = PdfWriter::sprintf('%sendbfrange', $this->writer->implode($ranges));
        }
        if ([] !== $chars) {
            $output[] = PdfWriter::sprintf('%d beginbfchar', \count($chars));
            $output[] = PdfWriter::sprintf('%sendbfchar', $this->writer->implode($chars));
        }
        $output[] = 'endcmap';
        $output[] = 'CMapName currentdict /CMap defineresource pop';
        $output[] = 'end';

        return $this->writer->implode($output) . 'end';
    }
}
