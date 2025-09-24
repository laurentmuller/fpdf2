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

$type = 'Core';
$name = 'Length2';
$length1 = 10;
$length2 = 10;
$originalsize = 10;
$size1 = 25;
$size2 = 30;
$file = 'font_length2.php';
$up = -100;
$ut = 50;
$diff = "138 /.notdef 142 /.notdef 154 /.notdef 158 /.notdef 195 /Abreve 204 /gravecomb 208 /Dcroat 210 /hookabovecomb 213 /Ohorn 221 /Uhorn /tildecomb 227 /abreve 236 /acutecomb 240 /dcroat 242 /dotbelowcomb 245 /ohorn 253 /uhorn /dong";
for ($i = 0; $i <= 255; ++$i) {
    $cw[\chr($i)] = 600;
}
$enc = 'cp1252';
$uv = [
    0 => [0, 128],
    128 => 8364,
    130 => 8218,
    131 => 402,
    132 => 8222,
    133 => 8230,
    134 => [8224, 2],
    136 => 710,
    137 => 8240,
    138 => 352,
    139 => 8249,
    140 => 338,
    142 => 381,
    145 => [8216, 2],
    147 => [8220, 2],
    149 => 8226,
    150 => [8211, 2],
    152 => 732,
    153 => 8482,
    154 => 353,
    155 => 8250,
    156 => 339,
    158 => 382,
    159 => 376,
    160 => [160, 96],
];
