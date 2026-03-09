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

final class PdfCompareMetadataTest extends AbstractCompareTestCase
{
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->getInfo()->setTitle('Title æ', true)
            ->setSubject('Subject')
            ->setAuthor('Author')
            ->setCreator('Creator')
            ->setKeywords('Keys words');
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
