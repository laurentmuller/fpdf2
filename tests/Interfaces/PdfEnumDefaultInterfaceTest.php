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

namespace fpdf\Tests\Interfaces;

use fpdf\Tests\Fixture\PdfInvalidEnumDefault;
use PHPUnit\Framework\TestCase;

final class PdfEnumDefaultInterfaceTest extends TestCase
{
    public function testNoDefaultFound(): void
    {
        self::expectException(\LogicException::class);
        self::expectExceptionMessage('No default value found for "fpdf\Tests\Fixture\PdfInvalidEnumDefault" enumeration.');
        PdfInvalidEnumDefault::getDefault();
    }
}
