<?php
/*
 *     FPDF
 *     Version: 1.86
 *     Date:    2023-06-25
 *     Author:  Olivier PLATHEY
 */

declare(strict_types=1);
/*******************************************************************************
* Class to parse and subset TrueType fonts                                     *
*                                                                              *
* Version: 1.11                                                                *
* Date:    2021-04-18                                                          *
* Author:  Olivier PLATHEY                                                     *
*******************************************************************************/

class ttfparser
{
    public $bold;
    public $capHeight;
    public $chars;
    public $embeddable;
    public $glyphs;
    public $isFixedPitch;
    public $italicAngle;
    public $postScriptName;
    public $typoAscender;
    public $typoDescender;
    public $underlinePosition;
    public $underlineThickness;
    public $unitsPerEm;
    public $xMax;
    public $xMin;
    public $yMax;
    public $yMin;
    protected $f;
    protected $glyphNames;
    protected $indexToLocFormat;
    protected $numberOfHMetrics;
    protected $numGlyphs;
    protected $subsettedChars;
    protected $subsettedGlyphs;
    protected $tables;

    public function __construct($file)
    {
        $this->f = \fopen($file, 'r');
        if (!$this->f) {
            $this->Error('Can\'t open file: ' . $file);
        }
    }

    public function __destruct()
    {
        if (\is_resource($this->f)) {
            \fclose($this->f);
        }
    }

    public function AddGlyph($id): void
    {
        if (!isset($this->glyphs[$id]['ssid'])) {
            $this->glyphs[$id]['ssid'] = \count($this->subsettedGlyphs);
            $this->subsettedGlyphs[] = $id;
            if (isset($this->glyphs[$id]['components'])) {
                foreach ($this->glyphs[$id]['components'] as $cid) {
                    $this->AddGlyph($cid);
                }
            }
        }
    }

    public function Build()
    {
        $this->BuildCmap();
        $this->BuildHhea();
        $this->BuildHmtx();
        $this->BuildLoca();
        $this->BuildGlyf();
        $this->BuildMaxp();
        $this->BuildPost();

        return $this->BuildFont();
    }

    public function BuildCmap(): void
    {
        if (!isset($this->subsettedChars)) {
            return;
        }

        // Divide charset in contiguous segments
        $chars = $this->subsettedChars;
        \sort($chars);
        $segments = [];
        $segment = [$chars[0], $chars[0]];
        for ($i = 1; $i < \count($chars); ++$i) {
            if ($chars[$i] > $segment[1] + 1) {
                $segments[] = $segment;
                $segment = [$chars[$i], $chars[$i]];
            } else {
                ++$segment[1];
            }
        }
        $segments[] = $segment;
        $segments[] = [0xFFFF, 0xFFFF];
        $segCount = \count($segments);

        // Build a Format 4 subtable
        $startCount = [];
        $endCount = [];
        $idDelta = [];
        $idRangeOffset = [];
        $glyphIdArray = '';
        for ($i = 0; $i < $segCount; ++$i) {
            [$start, $end] = $segments[$i];
            $startCount[] = $start;
            $endCount[] = $end;
            if ($start !== $end) {
                // Segment with multiple chars
                $idDelta[] = 0;
                $idRangeOffset[] = \strlen($glyphIdArray) + ($segCount - $i) * 2;
                for ($c = $start; $c <= $end; ++$c) {
                    $ssid = $this->glyphs[$this->chars[$c]]['ssid'];
                    $glyphIdArray .= \pack('n', $ssid);
                }
            } else {
                // Segment with a single char
                if ($start < 0xFFFF) {
                    $ssid = $this->glyphs[$this->chars[$start]]['ssid'];
                } else {
                    $ssid = 0;
                }
                $idDelta[] = $ssid - $start;
                $idRangeOffset[] = 0;
            }
        }
        $entrySelector = 0;
        $n = $segCount;
        while (1 !== $n) {
            $n = $n >> 1;
            ++$entrySelector;
        }
        $searchRange = (1 << $entrySelector) * 2;
        $rangeShift = 2 * $segCount - $searchRange;
        $cmap = \pack('nnnn', 2 * $segCount, $searchRange, $entrySelector, $rangeShift);
        foreach ($endCount as $val) {
            $cmap .= \pack('n', $val);
        }
        $cmap .= \pack('n', 0); // reservedPad
        foreach ($startCount as $val) {
            $cmap .= \pack('n', $val);
        }
        foreach ($idDelta as $val) {
            $cmap .= \pack('n', $val);
        }
        foreach ($idRangeOffset as $val) {
            $cmap .= \pack('n', $val);
        }
        $cmap .= $glyphIdArray;

        $data = \pack('nn', 0, 1); // version, numTables
        $data .= \pack('nnN', 3, 1, 12); // platformID, encodingID, offset
        $data .= \pack('nnn', 4, 6 + \strlen($cmap), 0); // format, length, language
        $data .= $cmap;
        $this->SetTable('cmap', $data);
    }

    public function BuildFont()
    {
        $tags = [];
        foreach (['cmap', 'cvt ', 'fpgm', 'glyf', 'head', 'hhea', 'hmtx', 'loca', 'maxp', 'name', 'post', 'prep'] as $tag) {
            if (isset($this->tables[$tag])) {
                $tags[] = $tag;
            }
        }
        $numTables = \count($tags);
        $offset = 12 + 16 * $numTables;
        foreach ($tags as $tag) {
            if (!isset($this->tables[$tag]['data'])) {
                $this->LoadTable($tag);
            }
            $this->tables[$tag]['offset'] = $offset;
            $offset += \strlen($this->tables[$tag]['data']);
        }

        // Build offset table
        $entrySelector = 0;
        $n = $numTables;
        while (1 !== $n) {
            $n = $n >> 1;
            ++$entrySelector;
        }
        $searchRange = 16 * (1 << $entrySelector);
        $rangeShift = 16 * $numTables - $searchRange;
        $offsetTable = \pack('nnnnnn', 1, 0, $numTables, $searchRange, $entrySelector, $rangeShift);
        foreach ($tags as $tag) {
            $table = $this->tables[$tag];
            $offsetTable .= $tag . $table['checkSum'] . \pack('NN', $table['offset'], $table['length']);
        }

        // Compute checkSumAdjustment (0xB1B0AFBA - font checkSum)
        $s = $this->CheckSum($offsetTable);
        foreach ($tags as $tag) {
            $s .= $this->tables[$tag]['checkSum'];
        }
        $a = \unpack('n2', $this->CheckSum($s));
        $high = 0xB1B0 + ($a[1] ^ 0xFFFF);
        $low = 0xAFBA + ($a[2] ^ 0xFFFF) + 1;
        $checkSumAdjustment = \pack('nn', $high + ($low >> 16), $low);
        $this->tables['head']['data'] = \substr_replace($this->tables['head']['data'], $checkSumAdjustment, 8, 4);

        $font = $offsetTable;
        foreach ($tags as $tag) {
            $font .= $this->tables[$tag]['data'];
        }

        return $font;
    }

    public function BuildGlyf(): void
    {
        $tableOffset = $this->tables['glyf']['offset'];
        $data = '';
        foreach ($this->subsettedGlyphs as $id) {
            $glyph = $this->glyphs[$id];
            \fseek($this->f, $tableOffset + $glyph['offset'], \SEEK_SET);
            $glyph_data = $this->Read($glyph['length']);
            if (isset($glyph['components'])) {
                // Composite glyph
                foreach ($glyph['components'] as $offset => $cid) {
                    $ssid = $this->glyphs[$cid]['ssid'];
                    $glyph_data = \substr_replace($glyph_data, \pack('n', $ssid), $offset, 2);
                }
            }
            $data .= $glyph_data;
        }
        $this->SetTable('glyf', $data);
    }

    public function BuildHhea(): void
    {
        $this->LoadTable('hhea');
        $numberOfHMetrics = \count($this->subsettedGlyphs);
        $data = \substr_replace($this->tables['hhea']['data'], \pack('n', $numberOfHMetrics), 4 + 15 * 2, 2);
        $this->SetTable('hhea', $data);
    }

    public function BuildHmtx(): void
    {
        $data = '';
        foreach ($this->subsettedGlyphs as $id) {
            $glyph = $this->glyphs[$id];
            $data .= \pack('nn', $glyph['w'], $glyph['lsb']);
        }
        $this->SetTable('hmtx', $data);
    }

    public function BuildLoca(): void
    {
        $data = '';
        $offset = 0;
        foreach ($this->subsettedGlyphs as $id) {
            if (0 === $this->indexToLocFormat) {
                $data .= \pack('n', $offset / 2);
            } else {
                $data .= \pack('N', $offset);
            }
            $offset += $this->glyphs[$id]['length'];
        }
        if (0 === $this->indexToLocFormat) {
            $data .= \pack('n', $offset / 2);
        } else {
            $data .= \pack('N', $offset);
        }
        $this->SetTable('loca', $data);
    }

    public function BuildMaxp(): void
    {
        $this->LoadTable('maxp');
        $numGlyphs = \count($this->subsettedGlyphs);
        $data = \substr_replace($this->tables['maxp']['data'], \pack('n', $numGlyphs), 4, 2);
        $this->SetTable('maxp', $data);
    }

    public function BuildPost(): void
    {
        $this->Seek('post');
        if ($this->glyphNames) {
            // Version 2.0
            $numberOfGlyphs = \count($this->subsettedGlyphs);
            $numNames = 0;
            $names = '';
            $data = $this->Read(2 * 4 + 2 * 2 + 5 * 4);
            $data .= \pack('n', $numberOfGlyphs);
            foreach ($this->subsettedGlyphs as $id) {
                $name = $this->glyphs[$id]['name'];
                if (\is_string($name)) {
                    $data .= \pack('n', 258 + $numNames);
                    $names .= \chr(\strlen($name)) . $name;
                    ++$numNames;
                } else {
                    $data .= \pack('n', $name);
                }
            }
            $data .= $names;
        } else {
            // Version 3.0
            $this->Skip(4);
            $data = "\x00\x03\x00\x00";
            $data .= $this->Read(4 + 2 * 2 + 5 * 4);
        }
        $this->SetTable('post', $data);
    }

    public function CheckSum($s)
    {
        $n = \strlen($s);
        $high = 0;
        $low = 0;
        for ($i = 0; $i < $n; $i += 4) {
            $high += (\ord($s[$i]) << 8) + \ord($s[$i + 1]);
            $low += (\ord($s[$i + 2]) << 8) + \ord($s[$i + 3]);
        }

        return \pack('nn', $high + ($low >> 16), $low);
    }

    public function Error($msg): void
    {
        throw new Exception($msg);
    }

    public function LoadTable($tag): void
    {
        $this->Seek($tag);
        $length = $this->tables[$tag]['length'];
        $n = $length % 4;
        if ($n > 0) {
            $length += 4 - $n;
        }
        $this->tables[$tag]['data'] = $this->Read($length);
    }

    public function Parse(): void
    {
        $this->ParseOffsetTable();
        $this->ParseHead();
        $this->ParseHhea();
        $this->ParseMaxp();
        $this->ParseHmtx();
        $this->ParseLoca();
        $this->ParseGlyf();
        $this->ParseCmap();
        $this->ParseName();
        $this->ParseOS2();
        $this->ParsePost();
    }

    public function ParseCmap(): void
    {
        $this->Seek('cmap');
        $this->Skip(2); // version
        $numTables = $this->ReadUShort();
        $offset31 = 0;
        for ($i = 0; $i < $numTables; ++$i) {
            $platformID = $this->ReadUShort();
            $encodingID = $this->ReadUShort();
            $offset = $this->ReadULong();
            if (3 === $platformID && 1 === $encodingID) {
                $offset31 = $offset;
            }
        }
        if (0 === $offset31) {
            $this->Error('No Unicode encoding found');
        }

        $startCount = [];
        $endCount = [];
        $idDelta = [];
        $idRangeOffset = [];
        $this->chars = [];
        \fseek($this->f, $this->tables['cmap']['offset'] + $offset31, \SEEK_SET);
        $format = $this->ReadUShort();
        if (4 !== $format) {
            $this->Error('Unexpected subtable format: ' . $format);
        }
        $this->Skip(2 * 2); // length, language
        $segCount = $this->ReadUShort() / 2;
        $this->Skip(3 * 2); // searchRange, entrySelector, rangeShift
        for ($i = 0; $i < $segCount; ++$i) {
            $endCount[$i] = $this->ReadUShort();
        }
        $this->Skip(2); // reservedPad
        for ($i = 0; $i < $segCount; ++$i) {
            $startCount[$i] = $this->ReadUShort();
        }
        for ($i = 0; $i < $segCount; ++$i) {
            $idDelta[$i] = $this->ReadShort();
        }
        $offset = \ftell($this->f);
        for ($i = 0; $i < $segCount; ++$i) {
            $idRangeOffset[$i] = $this->ReadUShort();
        }

        for ($i = 0; $i < $segCount; ++$i) {
            $c1 = $startCount[$i];
            $c2 = $endCount[$i];
            $d = $idDelta[$i];
            $ro = $idRangeOffset[$i];
            if ($ro > 0) {
                \fseek($this->f, $offset + 2 * $i + $ro, \SEEK_SET);
            }
            for ($c = $c1; $c <= $c2; ++$c) {
                if (0xFFFF === $c) {
                    break;
                }
                if ($ro > 0) {
                    $gid = $this->ReadUShort();
                    if ($gid > 0) {
                        $gid += $d;
                    }
                } else {
                    $gid = $c + $d;
                }
                if ($gid >= 65536) {
                    $gid -= 65536;
                }
                if ($gid > 0) {
                    $this->chars[$c] = $gid;
                }
            }
        }
    }

    public function ParseGlyf(): void
    {
        $tableOffset = $this->tables['glyf']['offset'];
        foreach ($this->glyphs as &$glyph) {
            if ($glyph['length'] > 0) {
                \fseek($this->f, $tableOffset + $glyph['offset'], \SEEK_SET);
                if ($this->ReadShort() < 0) {
                    // Composite glyph
                    $this->Skip(4 * 2); // xMin, yMin, xMax, yMax
                    $offset = 5 * 2;
                    $a = [];
                    do {
                        $flags = $this->ReadUShort();
                        $index = $this->ReadUShort();
                        $a[$offset + 2] = $index;
                        if ($flags & 1) { // ARG_1_AND_2_ARE_WORDS
                            $skip = 2 * 2;
                        } else {
                            $skip = 2;
                        }
                        if ($flags & 8) { // WE_HAVE_A_SCALE
                            $skip += 2;
                        } elseif ($flags & 64) { // WE_HAVE_AN_X_AND_Y_SCALE
                            $skip += 2 * 2;
                        } elseif ($flags & 128) { // WE_HAVE_A_TWO_BY_TWO
                            $skip += 4 * 2;
                        }
                        $this->Skip($skip);
                        $offset += 2 * 2 + $skip;
                    } while ($flags & 32); // MORE_COMPONENTS
                    $glyph['components'] = $a;
                }
            }
        }
    }

    public function ParseHead(): void
    {
        $this->Seek('head');
        $this->Skip(3 * 4); // version, fontRevision, checkSumAdjustment
        $magicNumber = $this->ReadULong();
        if (0x5F0F3CF5 !== $magicNumber) {
            $this->Error('Incorrect magic number');
        }
        $this->Skip(2); // flags
        $this->unitsPerEm = $this->ReadUShort();
        $this->Skip(2 * 8); // created, modified
        $this->xMin = $this->ReadShort();
        $this->yMin = $this->ReadShort();
        $this->xMax = $this->ReadShort();
        $this->yMax = $this->ReadShort();
        $this->Skip(3 * 2); // macStyle, lowestRecPPEM, fontDirectionHint
        $this->indexToLocFormat = $this->ReadShort();
    }

    public function ParseHhea(): void
    {
        $this->Seek('hhea');
        $this->Skip(4 + 15 * 2);
        $this->numberOfHMetrics = $this->ReadUShort();
    }

    public function ParseHmtx(): void
    {
        $this->Seek('hmtx');
        $this->glyphs = [];
        for ($i = 0; $i < $this->numberOfHMetrics; ++$i) {
            $advanceWidth = $this->ReadUShort();
            $lsb = $this->ReadShort();
            $this->glyphs[$i] = ['w' => $advanceWidth, 'lsb' => $lsb];
        }
        for ($i = $this->numberOfHMetrics; $i < $this->numGlyphs; ++$i) {
            $lsb = $this->ReadShort();
            $this->glyphs[$i] = ['w' => $advanceWidth, 'lsb' => $lsb];
        }
    }

    public function ParseLoca(): void
    {
        $this->Seek('loca');
        $offsets = [];
        if (0 === $this->indexToLocFormat) {
            // Short format
            for ($i = 0; $i <= $this->numGlyphs; ++$i) {
                $offsets[] = 2 * $this->ReadUShort();
            }
        } else {
            // Long format
            for ($i = 0; $i <= $this->numGlyphs; ++$i) {
                $offsets[] = $this->ReadULong();
            }
        }
        for ($i = 0; $i < $this->numGlyphs; ++$i) {
            $this->glyphs[$i]['offset'] = $offsets[$i];
            $this->glyphs[$i]['length'] = $offsets[$i + 1] - $offsets[$i];
        }
    }

    public function ParseMaxp(): void
    {
        $this->Seek('maxp');
        $this->Skip(4);
        $this->numGlyphs = $this->ReadUShort();
    }

    public function ParseName(): void
    {
        $this->Seek('name');
        $tableOffset = $this->tables['name']['offset'];
        $this->postScriptName = '';
        $this->Skip(2); // format
        $count = $this->ReadUShort();
        $stringOffset = $this->ReadUShort();
        for ($i = 0; $i < $count; ++$i) {
            $this->Skip(3 * 2); // platformID, encodingID, languageID
            $nameID = $this->ReadUShort();
            $length = $this->ReadUShort();
            $offset = $this->ReadUShort();
            if (6 === $nameID) {
                // PostScript name
                \fseek($this->f, $tableOffset + $stringOffset + $offset, \SEEK_SET);
                $s = $this->Read($length);
                $s = \str_replace(\chr(0), '', $s);
                $s = \preg_replace('|[ \[\](){}<>/%]|', '', $s);
                $this->postScriptName = $s;
                break;
            }
        }
        if ('' === $this->postScriptName) {
            $this->Error('PostScript name not found');
        }
    }

    public function ParseOffsetTable(): void
    {
        $version = $this->Read(4);
        if ('OTTO' === $version) {
            $this->Error('OpenType fonts based on PostScript outlines are not supported');
        }
        if ("\x00\x01\x00\x00" !== $version) {
            $this->Error('Unrecognized file format');
        }
        $numTables = $this->ReadUShort();
        $this->Skip(3 * 2); // searchRange, entrySelector, rangeShift
        $this->tables = [];
        for ($i = 0; $i < $numTables; ++$i) {
            $tag = $this->Read(4);
            $checkSum = $this->Read(4);
            $offset = $this->ReadULong();
            $length = $this->ReadULong();
            $this->tables[$tag] = ['offset' => $offset, 'length' => $length, 'checkSum' => $checkSum];
        }
    }

    public function ParseOS2(): void
    {
        $this->Seek('OS/2');
        $version = $this->ReadUShort();
        $this->Skip(3 * 2); // xAvgCharWidth, usWeightClass, usWidthClass
        $fsType = $this->ReadUShort();
        $this->embeddable = (2 !== $fsType) && ($fsType & 0x200) === 0;
        $this->Skip(11 * 2 + 10 + 4 * 4 + 4);
        $fsSelection = $this->ReadUShort();
        $this->bold = ($fsSelection & 32) !== 0;
        $this->Skip(2 * 2); // usFirstCharIndex, usLastCharIndex
        $this->typoAscender = $this->ReadShort();
        $this->typoDescender = $this->ReadShort();
        if ($version >= 2) {
            $this->Skip(3 * 2 + 2 * 4 + 2);
            $this->capHeight = $this->ReadShort();
        } else {
            $this->capHeight = 0;
        }
    }

    public function ParsePost(): void
    {
        $this->Seek('post');
        $version = $this->ReadULong();
        $this->italicAngle = $this->ReadShort();
        $this->Skip(2); // Skip decimal part
        $this->underlinePosition = $this->ReadShort();
        $this->underlineThickness = $this->ReadShort();
        $this->isFixedPitch = (0 !== $this->ReadULong());
        if (0x20000 === $version) {
            // Extract glyph names
            $this->Skip(4 * 4); // min/max usage
            $this->Skip(2); // numberOfGlyphs
            $glyphNameIndex = [];
            $names = [];
            $numNames = 0;
            for ($i = 0; $i < $this->numGlyphs; ++$i) {
                $index = $this->ReadUShort();
                $glyphNameIndex[] = $index;
                if ($index >= 258 && $index - 257 > $numNames) {
                    $numNames = $index - 257;
                }
            }
            for ($i = 0; $i < $numNames; ++$i) {
                $len = \ord($this->Read(1));
                $names[] = $this->Read($len);
            }
            foreach ($glyphNameIndex as $i => $index) {
                if ($index >= 258) {
                    $this->glyphs[$i]['name'] = $names[$index - 258];
                } else {
                    $this->glyphs[$i]['name'] = $index;
                }
            }
            $this->glyphNames = true;
        } else {
            $this->glyphNames = false;
        }
    }

    public function Read($n)
    {
        return $n > 0 ? \fread($this->f, $n) : '';
    }

    public function ReadShort()
    {
        $a = \unpack('nn', \fread($this->f, 2));
        $v = $a['n'];
        if ($v >= 0x8000) {
            $v -= 65536;
        }

        return $v;
    }

    public function ReadULong()
    {
        $a = \unpack('NN', \fread($this->f, 4));

        return $a['N'];
    }

    public function ReadUShort()
    {
        $a = \unpack('nn', \fread($this->f, 2));

        return $a['n'];
    }

    public function Seek($tag): void
    {
        if (!isset($this->tables[$tag])) {
            $this->Error('Table not found: ' . $tag);
        }
        \fseek($this->f, $this->tables[$tag]['offset'], \SEEK_SET);
    }

    public function SetTable($tag, $data): void
    {
        $length = \strlen($data);
        $n = $length % 4;
        if ($n > 0) {
            $data = \str_pad($data, $length + 4 - $n, "\x00");
        }
        $this->tables[$tag]['data'] = $data;
        $this->tables[$tag]['length'] = $length;
        $this->tables[$tag]['checkSum'] = $this->CheckSum($data);
    }

    public function Skip($n): void
    {
        \fseek($this->f, $n, \SEEK_CUR);
    }

    public function Subset($chars): void
    {
        $this->subsettedGlyphs = [];
        $this->AddGlyph(0);
        $this->subsettedChars = [];
        foreach ($chars as $char) {
            if (isset($this->chars[$char])) {
                $this->subsettedChars[] = $char;
                $this->AddGlyph($this->chars[$char]);
            }
        }
    }
}
