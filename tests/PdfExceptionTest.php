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

use PHPUnit\Framework\TestCase;

class PdfExceptionTest extends TestCase
{
    public function testFormat(): void
    {
        $values = [0.00, 1, 'fake'];
        $format = 'Float: %0.2f, Integer: %d, String: %s';
        $expected = \sprintf($format, ...$values);
        $exception = PdfException::instance($format, ...$values);
        self::assertSame($expected, $exception->getMessage());
    }

    public function testInstance(): void
    {
        $expected = 'The message';
        $exception = PdfException::instance($expected);
        self::assertSame($expected, $exception->getMessage());
    }

    public function testInvalidFormat(): void
    {
        $values = [0.00, 1];
        $format = 'Float: %0.2f, Integer: %d, String: %s';
        $exception = PdfException::instance($format, ...$values);
        self::assertSame($format, $exception->getMessage());
    }
}
