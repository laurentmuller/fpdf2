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
