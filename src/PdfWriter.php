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

use fpdf\Enums\PdfState;

/**
 * The PDF writer holds content and document state.
 */
class PdfWriter
{
    /** The new line separator. */
    public const string NEW_LINE = "\n";

    /** The buffer holding in-memory PDF. */
    private string $buffer = '';

    /** The compression flag. */
    private bool $compression = true;

    /** The object number. */
    private int $objectNumber = 2;

    /**
     * The object offsets.
     *
     * @var array<int, int>
     */
    private array $offsets = [];

    /**
     * The pages.
     *
     * @var array<int, string>
     */
    private array $pages = [];

    /** The current state. */
    private PdfState $state = PdfState::NO_PAGE;

    /** The thousand separator. */
    private ?string $thousandsSep = null;

    /**
     * Initialize a new page. After this call, this current state is {@link PdfState::PAGE_STARTED}.
     *
     * @param int $page the page number to initialize
     *
     * @throws PdfException if unable to move to the {@link PdfState::PAGE_STARTED} state
     */
    public function beginPage(int $page): self
    {
        if (!$this->state->isAllowed(PdfState::PAGE_STARTED)) {
            throw PdfException::format('Unable to move state from "%s" to "%s".', $this->state, PdfState::PAGE_STARTED);
        }
        $this->pages[$page] = '';
        $this->state = PdfState::PAGE_STARTED;

        return $this;
    }

    /**
     * Set this current state to {@link PdfState::CLOSED}.
     *
     * @throws PdfException if unable to move to the {@link PdfState::CLOSED} state
     */
    public function close(): self
    {
        if (!$this->state->isAllowed(PdfState::CLOSED)) {
            throw PdfException::format('Unable to move state from "%s" to "%s".', $this->state, PdfState::CLOSED);
        }
        $this->state = PdfState::CLOSED;

        return $this;
    }

    /**
     * Set this current state to {@link PdfState::END_PAGE}.
     *
     * @throws PdfException if unable to move to the {@link PdfState::END_PAGE} state
     */
    public function endPage(): self
    {
        if (!$this->state->isAllowed(PdfState::END_PAGE)) {
            throw PdfException::format('Unable to move state from "%s" to "%s".', $this->state, PdfState::END_PAGE);
        }
        $this->state = PdfState::END_PAGE;

        return $this;
    }

    /**
     * Format the given object number for output.
     */
    public static function formatNumber(int $number): string
    {
        return self::sprintf('%d 0 R', $number);
    }

    /**
     * Gets the buffer holding in-memory PDF.
     */
    public function getBuffer(): string
    {
        return $this->buffer;
    }

    /**
     * Gets the object number.
     */
    public function getObjectNumber(): int
    {
        return $this->objectNumber;
    }

    /**
     * Gets the buffer offset.
     */
    public function getOffset(): int
    {
        return \strlen($this->buffer);
    }

    /**
     * Gets the object offsets.
     *
     * @return array<int, int>
     */
    public function getOffsets(): array
    {
        return $this->offsets;
    }

    /**
     * Gets a value indicating if this current state is {@link PdfState::CLOSED}.
     *
     * @return bool true if closed
     */
    public function isClosed(): bool
    {
        return PdfState::CLOSED === $this->state;
    }

    /**
     * Outputs the specified content to a given page.
     *
     * @throws PdfException if no page has been added, if the end page has been called, or if the document is closed
     */
    public function out(int $page, \BackedEnum|\UnitEnum|float|string|int $output): self
    {
        switch ($this->state) {
            case PdfState::NO_PAGE:
                throw PdfException::instance('Invalid call: No page added.');
            case PdfState::END_PAGE:
                throw PdfException::instance('Invalid call: End page.');
            case PdfState::CLOSED:
                throw PdfException::instance('Invalid call: Document closed.');
            case PdfState::PAGE_STARTED:
                $this->pages[$page] .= self::convertValue($output) . self::NEW_LINE;
                break;
        }

        return $this;
    }

    /**
     * Output a formatted string to a given page.
     *
     * @param int                                    $page      the page number where the output should be added
     * @param string                                 $format    the format string
     * @param \BackedEnum|\UnitEnum|float|string|int ...$values the values to be formatted
     *
     * @throws PdfException if no page has been added, if the end page has been called, or if the document is closed
     */
    public function outf(int $page, string $format, \BackedEnum|\UnitEnum|float|string|int ...$values): self
    {
        return $this->out($page, self::sprintf($format, ...$values));
    }

    /**
     * Put the given value to this buffer.
     */
    public function put(\BackedEnum|\UnitEnum|float|string|int $value): self
    {
        $this->buffer .= self::convertValue($value) . self::NEW_LINE;

        return $this;
    }

    /**
     * Put the end object to this buffer.
     *
     * @see PdfWriter::putNewObj()
     */
    public function putEndObj(): self
    {
        return $this->put('endobj');
    }

    /**
     * Put a formatted string to this buffer.
     *
     * @param string                                 $format    the format string
     * @param \BackedEnum|\UnitEnum|float|string|int ...$values the values to be formatted
     */
    public function putf(string $format, \BackedEnum|\UnitEnum|float|string|int ...$values): self
    {
        return $this->put(self::sprintf($format, ...$values));
    }

    /**
     * Put a new object to this buffer.
     *
     * @param int|null $index the object number or null to use the next available object number
     *
     * @see PdfWriter::putEndObj()
     */
    public function putNewObj(?int $index = null): self
    {
        $index ??= ++$this->objectNumber;
        $this->offsets[$index] = $this->getOffset();
        $this->putf('%d 0 obj', $index);

        return $this;
    }

    /**
     * Put the stream string to this buffer.
     */
    public function putStream(string $data): self
    {
        $this->put('stream');
        $this->put($data);
        $this->put('endstream');

        return $this;
    }

    /**
     * Put the stream object to this buffer.
     *
     * @param string $data the data to put in the buffer
     */
    public function putStreamObject(string $data): self
    {
        $entries = '';
        if ($this->compression) {
            $entries = '/Filter /FlateDecode ';
            $data = (string) \gzcompress($data);
        }
        $entries .= self::sprintf('/Length %d', \strlen($data));
        $this->putNewObj();
        $this->putf('<<%s>>', $entries);
        $this->putStream($data);
        $this->putEndObj();

        return $this;
    }

    /**
     * Put the content of the given page, as a stream object, to this buffer.
     *
     * @param int $page the page to get content for
     */
    public function putStreamPage(int $page): self
    {
        $this->putStreamObject($this->pages[$page]);

        return $this;
    }

    /**
     * Activates or deactivates page compression.
     *
     * When activated, the internal representation of each page is compressed, which leads to a compression ratio of
     * about 2 for the resulting document. Compression is on by default.
     */
    public function setCompression(bool $compression): self
    {
        $this->compression = $compression;

        return $this;
    }

    /**
     * Gets a formatted string.
     *
     * @param string                                 $format    the format string
     * @param \BackedEnum|\UnitEnum|float|string|int ...$values the values to be formatted
     *
     * @return string the formatted string
     */
    public static function sprintf(string $format, \BackedEnum|\UnitEnum|float|string|int ...$values): string
    {
        $values = self::convertValues(...$values);

        return \sprintf($format, ...$values);
    }

    /**
     * Replace the alias number pages by the given number of pages.
     *
     * @param int    $page             the page to update
     * @param int    $pages            the number of pages to replace alias for
     * @param string $aliasNumberPages the number pages alias
     */
    public function updateAliasNumberPages(int $page, int $pages, string $aliasNumberPages): self
    {
        $replace = \number_format(num: $pages, thousands_separator: $this->getThousandsSep());
        $this->pages[$page] = \str_replace($aliasNumberPages, $replace, $this->pages[$page]);

        return $this;
    }

    private static function convertValue(\BackedEnum|\UnitEnum|float|string|int $value): string|int|float
    {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }
        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        return $value;
    }

    /**
     * @return array<float|int|string>
     */
    private static function convertValues(\BackedEnum|\UnitEnum|float|string|int ...$values): array
    {
        return \array_map(self::convertValue(...), $values);
    }

    /**
     * Gets the thousand separator.
     */
    private function getThousandsSep(): string
    {
        /** @phpstan-ignore cast.string */
        return $this->thousandsSep ??= (string) \localeconv()['thousands_sep'];
    }
}
