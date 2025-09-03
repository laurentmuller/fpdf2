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

namespace fpdf\Tests;

use fpdf\PdfDocument;
use fpdf\Tests\Legacy\FPDF;

class PdfCompareMetadataTest extends AbstractCompareTestCase
{
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->setTitle('Title æ', true);
        $doc->setSubject('Subject');
        $doc->setAuthor('Author');
        $doc->setCreator('Creator');
        $doc->setKeywords('Keys words');
    }

    #[\Override]
    protected function updateOldDocument(FPDF $doc): void
    {
        $doc->SetTitle('Title æ', true);
        $doc->SetSubject('Subject');
        $doc->SetAuthor('Author');
        $doc->SetCreator('Creator');
        $doc->SetKeywords('Keys words');
    }
}
