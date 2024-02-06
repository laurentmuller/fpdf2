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

class MetadataTest extends AbstractTestCase
{
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->setTitle('Title');
        $doc->setSubject('Subject');
        $doc->setAuthor('Author');
        $doc->setCreator('Creator');
        $doc->setKeywords('Keys words');
    }

    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->SetTitle('Title');
        $doc->SetSubject('Subject');
        $doc->SetAuthor('Author');
        $doc->SetCreator('Creator');
        $doc->SetKeywords('Keys words');
    }
}
