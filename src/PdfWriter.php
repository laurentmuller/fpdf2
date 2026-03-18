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
    protected string $buffer = '';

    /** The compression flag. */
    protected bool $compression = true;

    /** The object number. */
    protected int $objectNumber = 2;

    /**
     * The object offsets.
     *
     * @var array<int, int>
     */
    protected array $offsets = [];

    /**
     * The pages.
     *
     * @var array<int, string>
     */
    protected array $pages = [];

    /** The current state. */
    protected PdfState $state = PdfState::NO_PAGE;

    /**
     * Initialize a new page.
     *
     * @param int $page the page number to initialize
     */
    public function beginPage(int $page): self
    {
        $this->pages[$page] = '';

        return $this;
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
     * Gets the content of the given page.
     *
     * @param int $page the page to get content for
     */
    public function getPage(int $page): string
    {
        return $this->pages[$page] ?? '';
    }

    /**
     * Gets the content of pages.
     *
     * @return array<int, string>
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * Gets the current state.
     */
    public function getState(): PdfState
    {
        return $this->state;
    }

    /**
     * Outputs the specified content to a given page.
     *
     * @param int    $page   the page number where the output should be added
     * @param string $output the content to be written to the specified
     *
     * @throws PdfException if no page has been added, if the end page has been called, or if the document is closed
     */
    public function out(int $page, string $output): self
    {
        switch ($this->state) {
            case PdfState::NO_PAGE:
                throw PdfException::instance('Invalid call: No page added.');
            case PdfState::END_PAGE:
                throw PdfException::instance('Invalid call: End page.');
            case PdfState::CLOSED:
                throw PdfException::instance('Invalid call: Document closed.');
            case PdfState::PAGE_STARTED:
                $this->pages[$page] .= $output . self::NEW_LINE;
                break;
        }

        return $this;
    }

    /**
     * Output a formatted string to a given page.
     *
     * @param int              $page      the page number where the output should be added
     * @param string           $format    the format string
     * @param string|int|float ...$values the values to be formatted
     *
     * @throws PdfException if no page has been added, if the end page has been called, or if the document is closed
     */
    public function outf(int $page, string $format, string|int|float ...$values): self
    {
        return $this->out($page, \sprintf($format, ...$values));
    }

    /**
     * Put the given value to this buffer.
     */
    public function put(string|int $value): self
    {
        $this->buffer .= \sprintf('%s%s', $value, self::NEW_LINE);

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
     * @param string           $format    the format string
     * @param string|int|float ...$values the values to be formatted
     */
    public function putf(string $format, string|int|float ...$values): self
    {
        return $this->put(\sprintf($format, ...$values));
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
        $entries .= \sprintf('/Length %d', \strlen($data));
        $this->putNewObj();
        $this->putf('<<%s>>', $entries);
        $this->putStream($data);
        $this->putEndObj();

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
     * sets the current state.
     */
    public function setState(PdfState $state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Replace the alias number page by the given page number.
     *
     * @param int    $page             the page to update
     * @param string $aliasNumberPages the alias number page
     */
    public function updateAliasNumberPages(int $page, string $aliasNumberPages): self
    {
        /** @var string $thousandsSep */
        $thousandsSep = \localeconv()['thousands_sep'];
        $replace = \number_format(num: $page, thousands_separator: $thousandsSep);
        $this->pages[$page] = \str_replace($aliasNumberPages, $replace, $this->pages[$page]);

        return $this;
    }
}
