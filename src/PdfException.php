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

namespace fpdf;

/**
 * This exception is raise in case of a fatal error.
 */
class PdfException extends \RuntimeException
{
    /**
     * Create a new instance for the given string format and values.
     *
     * @param string                                 $format    the string format
     * @param \BackedEnum|\UnitEnum|float|string|int ...$values the values
     *
     * @see PdfWriter::sprintf()
     */
    public static function format(string $format, \BackedEnum|\UnitEnum|float|string|int ...$values): self
    {
        try {
            return new self(PdfWriter::sprintf($format, ...$values));
        } catch (\Error) {
            return new self($format);
        }
    }

    /**
     * Create a new instance for the given message.
     *
     * @param string      $message  the message to throw
     * @param ?\Throwable $previous the previous throwable used for the exception chaining
     */
    public static function instance(string $message, ?\Throwable $previous = null): self
    {
        return new self($message, previous: $previous);
    }
}
