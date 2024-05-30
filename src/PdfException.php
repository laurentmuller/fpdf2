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

/**
 * This exception is raise in case of a fatal error.
 */
class PdfException extends \RuntimeException
{
    /**
     * Create a new instance for the given string format and values.
     *
     * @param string           $format    the string format
     * @param float|int|string ...$values the values
     *
     * @see https://www.php.net/manual/en/function.sprintf.php sprintf
     */
    public static function format(string $format, float|int|string ...$values): self
    {
        try {
            return new self(\sprintf($format, ...$values));
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
