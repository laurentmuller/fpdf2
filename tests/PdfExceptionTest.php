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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PdfException::class)]
class PdfExceptionTest extends TestCase
{
    public function testFormat(): void
    {
        $values = [0.00, 1, 'fake'];
        $format = 'Float: %0.2f, Integer: %d, String: %s';
        $expected = \sprintf($format, ...$values);
        $actual = PdfException::format($format, ...$values);
        self::assertSame($expected, $actual->getMessage());
    }

    public function testInstance(): void
    {
        $expected = 'The message';
        $actual = PdfException::instance($expected);
        self::assertSame($expected, $actual->getMessage());
        self::assertNull($actual->getPrevious());
    }

    public function testInstanceWithPrevious(): void
    {
        $message = 'The message';
        $previous = new \InvalidArgumentException();
        $actual = PdfException::instance($message, $previous);
        self::assertSame($message, $actual->getMessage());
        self::assertSame($previous, $actual->getPrevious());
    }

    public function testInvalidFormat(): void
    {
        $values = [0.00, 1];
        $format = 'Float: %0.2f, Integer: %d, String: %s';
        $actual = PdfException::format($format, ...$values);
        self::assertSame($format, $actual->getMessage());
    }
}
