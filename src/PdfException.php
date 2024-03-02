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
     * Create a new instance for the given message or format and values.
     *
     * @param string           $message   the format string, if values are provided, the message otherwise
     * @param float|int|string ...$values the values
     */
    public static function instance(string $message, float|int|string ...$values): self
    {
        if ([] !== $values) {
            try {
                return new self(\sprintf($message, ...$values));
            } catch (\Error) {
            }
        }

        return new self($message);
    }
}
