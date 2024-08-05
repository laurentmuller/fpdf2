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

use fpdf\fixture\PdfInvalidEnumDefault;
use PHPUnit\Framework\TestCase;

class PdfEnumDefaultInterfaceTest extends TestCase
{
    public function testNoDefaultFound(): void
    {
        self::expectException(\LogicException::class);
        self::expectExceptionMessage('No default value found for "fpdf\fixture\PdfInvalidEnumDefault" enumeration.');
        PdfInvalidEnumDefault::getDefault();
    }
}
