<?php

/*
 * This file is part of the 'fpdf' package.
 *
 * For the license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Andreas Müller <hello@devmount.com>
 */

declare(strict_types=1);

namespace fpdf\Traits;

use Exception;

/**
 * Trait to add file attachment support.
 *
 * The code is inspired from FPDF script
 * <a href="http://www.fpdf.org/en/script/script95.php" target="_blank">Attachment</a>.
 * 
 * @phpstan-type PdfAttachedFileType = array{
 *      file: string,
 *      name: string,
 *      desc: string}
 *
 * @phpstan-require-extends PdfDocument
 */
trait PdfAttachmentTrait
{
    /**
     * The attached files.
     *
     * @phpstan-var PdfAttachedFileType[]
     */
    private array $files = [];

    /**
     * The files object number.
     */
    private int $nFiles;

    /**
     * Flag to open the attachment pane in a PDF reader per default
     */
    private bool $openAttachmentPane = false;

        
    /**
     * Attaches a given file to the PDF document.
     *
     * @param  string $file Path to the file to be attached.
     * @param  string $name Optional alternative file name to be used for the attachment. The default value is taken
     *                      from $file.
     * @param  string $desc Optional description for the file contents.
     * @return static
     */
    public function attach(string $file, string $name = '', string $desc = ''): static
    {
        if ($name == '') {
            $p = strrpos($file, '/');
            if ($p === false) {
                $p = strrpos($file, '\\');
            }
            if ($p !== false) {
                $name = substr($file, $p + 1);
            }
            else {
                $name = $file;
            }
        }
        $this->files[] = ['file' => $file, 'name' => $name, 'desc' => $desc];
        return $this;
    }

    
    /**
     * Forces the attachment pane to open in PDF viewers that support it.
     *
     * @return static
     */
    public function openAttachmentPane(): static
    {
        $this->openAttachmentPane = true;
        return $this;
    }

    private function putFiles()
    {
        foreach ($this->files as $i => &$info) {
            $file = $info['file'];
            $name = $info['name'];
            $desc = $info['desc'];

            $fc = file_get_contents($file);
            if ($fc === false) {
                $this->error('Cannot open file: ' . $file);
            }
            $size = strlen($fc);
            $date = @date('YmdHisO', filemtime($file));
            $md = 'D:' . substr($date, 0, -2) . "'" . substr($date, -2) . "'";;

            $this->putNewObj();
            $info['n'] = $this->objectNumber;
            $this->put('<<');
            $this->put('/Type /Filespec');
            $this->put('/F (' . $this->escape($name) . ')');
            $this->put('/UF ' . $this->textstring($name));
            $this->put('/EF <</F ' . ($this->objectNumber + 1) . ' 0 R>>');
            if ($desc) {
                $this->put('/Desc ' . $this->textstring($desc));
            }
            $this->put('/AFRelationship /Unspecified');
            $this->put('>>');
            $this->put('endobj');

            $this->putNewObj();
            $this->put('<<');
            $this->put('/Type /EmbeddedFile');
            $this->put('/Subtype /application#2Foctet-stream');
            $this->put('/Length ' . $size);
            $this->put('/Params <</Size ' . $size . ' /ModDate ' . $this->textstring($md) . '>>');
            $this->put('>>');
            $this->putstream($fc);
            $this->put('endobj');
        }
        unset($info);

        $this->putNewObj();
        $this->nFiles = $this->objectNumber;
        $a = array();
        foreach ($this->files as $i => $info) {
            $a[] = $this->textstring(sprintf('%03d', $i)) . ' ' . $info['n'] . ' 0 R';
        }
        $this->put('<<');
        $this->put('/Names [' . implode(' ', $a) . ']');
        $this->put('>>');
        $this->put('endobj');
    }

    protected function putResources(): void
    {
        parent::putResources();
        if (!empty($this->files)) {
            $this->putFiles();
        }
    }

    protected function putCatalog(): void
    {
        parent::putCatalog();
        if (!empty($this->files)) {
            $this->put('/Names <</EmbeddedFiles ' . $this->nFiles . ' 0 R>>');
            $a = array();
            foreach ($this->files as $info) {
                $a[] = $info['n'] . ' 0 R';
            }
            $this->put('/AF [' . implode(' ', $a) . ']');
            if ($this->openAttachmentPane) {
                $this->put('/PageMode /UseAttachments');
            }
        }
    }

    private function error(string $msg)
    {
        // Fatal error
        throw new Exception('FPDF2 error: ' . $msg);
    }
}
