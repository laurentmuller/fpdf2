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
 * Contains document information (meta-data).
 */
class PdfDocumentInfo
{
    /** @var array<string, string> the meta-data */
    private array $metadata = [];

    /** @var string the title */
    private string $title = '';

    public function __construct(private readonly PdfEncoder $encoder) {}

    /**
     * Gets the meta-data.
     *
     * @return array<string, string>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * Gets the title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Defines the author of the document.
     *
     * @param string $author the name of the author
     * @param bool   $isUTF8 indicates if the string is encoded in ISO-8859-1 (<code>false</code>) or
     *                       UTF-8 (<code>true</code>)
     */
    public function setAuthor(string $author, bool $isUTF8 = false): self
    {
        return $this->addMetaData('Author', $author, $isUTF8);
    }

    /**
     * Sets the creation date.
     *
     * @param int|null $timestamp the creation date or null to use the current local time
     */
    public function setCreationDate(?int $timestamp = null): self
    {
        return $this->addMetaData('CreationDate', $this->encoder->formatDate($timestamp));
    }

    /**
     * Defines the creator of the document.
     *
     * This is typically the name of the application that generates the PDF.
     *
     * @param string $creator the name of the creator
     * @param bool   $isUTF8  indicates if the string is encoded in ISO-8859-1 (<code>false</code>)
     *                        or UTF-8 (<code>true</code>)
     */
    public function setCreator(string $creator, bool $isUTF8 = false): self
    {
        return $this->addMetaData('Creator', $creator, $isUTF8);
    }

    /**
     * Associates keywords with the document.
     *
     * @param string $keywords the list of keywords separated by space
     * @param bool   $isUTF8   indicates if the string is encoded in ISO-8859-1 (<code>false</code>)
     *                         or UTF-8 (<code>true</code>)
     */
    public function setKeywords(string $keywords, bool $isUTF8 = false): self
    {
        return $this->addMetaData('Keywords', $keywords, $isUTF8);
    }

    /**
     * Defines the producer of the document.
     *
     * @param string $producer the producer
     * @param bool   $isUTF8   indicates if the string is encoded in ISO-8859-1 (<code>false</code>)
     *                         or UTF-8 (<code>true</code>)
     */
    public function setProducer(string $producer, bool $isUTF8 = false): self
    {
        return $this->addMetaData('Producer', $producer, $isUTF8);
    }

    /**
     * Defines the subject of the document.
     *
     * @param string $subject the subject
     * @param bool   $isUTF8  indicates if the string is encoded in ISO-8859-1 (<code>false</code>)
     *                        or UTF-8 (<code>true</code>)
     */
    public function setSubject(string $subject, bool $isUTF8 = false): self
    {
        return $this->addMetaData('Subject', $subject, $isUTF8);
    }

    /**
     * Defines the title of the document.
     *
     * @param string $title  the title
     * @param bool   $isUTF8 indicates if the string is encoded in ISO-8859-1 (<code>false</code>)
     *                       or UTF-8 (<code>true</code>)
     */
    public function setTitle(string $title, bool $isUTF8 = false): self
    {
        $this->title = $title;

        return $this->addMetaData('Title', $title, $isUTF8);
    }

    /**
     * Add the given key/value pair to this meta-data.
     *
     * @param string $key    the meta-data key
     * @param string $value  the meta-data value
     * @param bool   $isUTF8 indicates if the value is encoded in ISO-8859-1 (<code>false</code>)
     *                       or UTF-8 (<code>true</code>)
     */
    protected function addMetaData(string $key, string $value, bool $isUTF8 = false): self
    {
        $this->metadata[$key] = $isUTF8 ? $value : $this->encoder->convertIsoToUtf8($value);

        return $this;
    }
}
