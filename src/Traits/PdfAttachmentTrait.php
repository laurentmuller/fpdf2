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

namespace fpdf\Traits;

use fpdf\Enums\PdfVersion;
use fpdf\Internal\PdfAttachment;
use fpdf\PdfDocument;
use fpdf\PdfException;

/**
 * Trait to add file attachment support.
 *
 * The code is inspired from FPDF script
 * <a href="http://www.fpdf.org/en/script/script95.php" target="_blank">Attachment</a>.
 *
 * @author Andreas Müller <hello@devmount.com>
 *
 * @phpstan-require-extends PdfDocument
 */
trait PdfAttachmentTrait
{
    /**
     * The attachement object number.
     */
    private int $attachmentNumber = -1;

    /**
     * The attached files.
     *
     * @var array<int, PdfAttachment>
     */
    private array $attachments = [];

    /**
     * Attaches a given file to the PDF document.
     *
     * @param string $file        path to the file to be attached
     * @param string $name        an optional alternative file name to be used for the attachment.
     *                            The default value is the base name of the file.
     * @param string $description an optional description for the file contents
     */
    public function addAttachment(string $file, string $name = '', string $description = ''): static
    {
        $this->attachments[] = new PdfAttachment(
            file: $file,
            name: '' === $name ? \basename($file) : $name,
            description: $description
        );
        $this->updatePdfVersion('' === $description ? PdfVersion::VERSION_1_4 : PdfVersion::VERSION_1_6);

        return $this;
    }

    protected function putCatalog(): void
    {
        parent::putCatalog();
        if ([] === $this->attachments) {
            return;
        }
        $this->putf('/Names <</EmbeddedFiles %d 0 R>>', $this->attachmentNumber);
        $array = \array_map(
            static fn (PdfAttachment $attachment): string => \sprintf('%d 0 R', $attachment->number),
            $this->attachments
        );
        $this->putf('/AF [%s]', \implode(' ', $array));
    }

    protected function putResources(): void
    {
        parent::putResources();
        if ([] !== $this->attachments) {
            $this->putAttachments();
        }
    }

    /**
     * @throws PdfException if unable to get the file content of an attachment
     */
    private function putAttachments(): void
    {
        foreach ($this->attachments as $attachment) {
            $file = $attachment->file;
            $name = $attachment->name;

            $contents = \file_get_contents($file);
            if (false === $contents) {
                throw PdfException::format('Unable to get content of the file: %s.', $file);
            }
            $size = \strlen($contents);
            $time = \filemtime($file);
            $date = $this->formatDate(\is_int($time) ? $time : null);

            $this->putNewObj();
            $attachment->number = $this->objectNumber;
            $this->put('<<');
            $this->put('/Type /Filespec');
            $this->putf('/F (%s)', $this->escape($name));
            $this->putf('/UF %s', $this->textString($name));
            $this->putf('/EF <</F %d 0 R>>', $this->objectNumber + 1);
            if ($attachment->isDescription()) {
                $this->putf('/Desc %s', $this->textString($attachment->description));
            }
            $this->put('/AFRelationship /Unspecified');
            $this->put('>>');
            $this->putEndObj();

            $this->putNewObj();
            $this->put('<<');
            $this->put('/Type /EmbeddedFile');
            $this->put('/Subtype /application#2Foctet-stream');
            $this->putf('/Length %d', $size);
            $this->putf('/Params <</Size %d /ModDate %s>>', $size, $this->textString($date));
            $this->put('>>');
            $this->putStream($contents);
            $this->putEndObj();
        }

        $this->putNewObj();
        $this->attachmentNumber = $this->objectNumber;
        $array = \array_map(
            fn (int $index, PdfAttachment $attachment): string => $this->textString(\sprintf('%03d %d 0 R', $index, $attachment->number)),
            \array_keys($this->attachments),
            \array_values($this->attachments)
        );
        $this->put('<<');
        $this->putf('/Names [%s]', \implode(' ', $array));
        $this->put('>>');
        $this->putEndObj();
    }
}
