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
 * Utility class to convert string and date.
 */
class PdfEncoder
{
    /**
     * Convert a string from one character encoding to another.
     *
     * @param string          $str           the string to be converted
     * @param string          $to_encoding   the desired encoding of the result
     * @param string[]|string $from_encoding the current encoding used to interpret string
     *
     * @return string the encoded string
     */
    public function convertEncoding(string $str, string $to_encoding, array|string $from_encoding): string
    {
        /** @var string */
        return \mb_convert_encoding($str, $to_encoding, $from_encoding);
    }

    /**
     * Convert the given string from ISO-8859-1 to UTF-8.
     */
    public function convertIsoToUtf8(string $str): string
    {
        return $this->convertEncoding($str, 'UTF-8', 'ISO-8859-1');
    }
    /**
     * Convert the given string from UTF8 to UTF-16BE.
     */
    public function convertUtf8ToUtf16(string $str): string
    {
        return "\xFE\xFF" . $this->convertEncoding($str, 'UTF-16BE', 'UTF-8');
    }

    /**
     * Escape special characters from the given string.
     */
    public function escape(string $str): string
    {
        return \str_replace(
            ['\\', '(', ')', "\r"],
            ['\\\\', '\\(', '\\)', '\\r'],
            $str
        );
    }

    /**
     * Format the given timestamp.
     *
     * @param ?int $timestamp the timestamp to format or the current local time if the timestamp is null
     */
    public function formatDate(?int $timestamp = null): string
    {
        $date = \date('YmdHisO', $timestamp);

        return \sprintf("D:%s'%s'", \substr($date, 0, -2), \substr($date, -2));
    }

    /**
     * Returns if the given string is valid for the <code>ASCII</code> encoding.
     *
     * @return bool <code>true</code> if the given string is only containing <code>ASCII</code> characters
     */
    public function isAscii(string $str): bool
    {
        return \mb_check_encoding($str, 'ASCII');
    }

    /**
     * Convert the given string.
     */
    public function textString(string $str): string
    {
        if (!$this->isAscii($str)) {
            $str = $this->convertUtf8ToUtf16($str);
        }

        return \sprintf('(%s)', $this->escape($str));
    }
}
