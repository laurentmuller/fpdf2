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
 * @phpstan-type PdfAttachedFileType = array{
 *      file: string,
 *      name: string,
 *      desc: string,
 *      n: int}
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
     * @phpstan-var array<int, PdfAttachedFileType>
     */
    private array $attachments = [];

    /**
     * Attaches a given file to the PDF document.
     *
     * @param string $file path to the file to be attached
     * @param string $name an optional alternative file name to be used for the attachment. The default value is the
     *                     base name of the file.
     * @param string $desc an optional description for the file contents
     */
    public function attach(string $file, string $name = '', string $desc = ''): static
    {
        if ('' === $name) {
            $name = \basename($file);
        }
        $this->attachments[] = ['file' => $file, 'name' => $name, 'desc' => $desc, 'n' => -1];

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
            static fn (array $attachment): string => \sprintf('%d 0 R', $attachment['n']),
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

    private function putAttachments(): void
    {
        foreach ($this->attachments as &$info) {
            $file = $info['file'];
            $name = $info['name'];
            $desc = $info['desc'];

            $contents = \file_get_contents($file);
            if (false === $contents) {
                throw PdfException::format('Cannot get content of the file: "%s".', $file);
            }
            $size = \strlen($contents);
            $time = \filemtime($file);
            $date = \date('YmdHisO', false === $time ? \time() : $time);
            $md = \sprintf("D: %s '%s'", \substr($date, 0, -2), \substr($date, -2));

            $this->putNewObj();
            $info['n'] = $this->objectNumber;
            $this->put('<<');
            $this->put('/Type /Filespec');
            $this->putf('/F (%s)', $this->escape($name));
            $this->putf('/UF %s', $this->textString($name));
            $this->putf('/EF <</F %d 0 R>>', $this->objectNumber + 1);
            if ('' !== $desc) {
                $this->putf('/Desc %s', $this->textString($desc));
            }
            $this->put('/AFRelationship /Unspecified');
            $this->put('>>');
            $this->putEndObj();

            $this->putNewObj();
            $this->put('<<');
            $this->put('/Type /EmbeddedFile');
            $this->put('/Subtype /application#2Foctet-stream');
            $this->putf('/Length %d', $size);
            $this->putf('/Params <</Size %d /ModDate %s>>', $size, $this->textString($md));
            $this->put('>>');
            $this->putStream($contents);
            $this->putEndObj();
        }
        unset($info);

        $this->putNewObj();
        $this->attachmentNumber = $this->objectNumber;
        $array = \array_map(
            fn (int $index, array $info): string => $this->textString(\sprintf('%03d %d 0 R', $index, $info['n'])),
            \array_keys($this->attachments),
            \array_values($this->attachments)
        );
        $this->put('<<');
        $this->putf('/Names [%s]', \implode(' ', $array));
        $this->put('>>');
        $this->putEndObj();
    }
}
