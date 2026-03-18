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

namespace fpdf\Tests;

use fpdf\PdfDocument;

final class PdfCompareMetadataTest extends AbstractCompareTestCase
{
    #[\Override]
    protected function updateNewDocument(PdfDocument $doc): void
    {
        $doc->getProperties()->setTitle('Title æ', true)
            ->setSubject('Subject')
            ->setAuthor('Author')
            ->setCreator('Creator')
            ->setKeywords('Keys words');
    }

    #[\Override]
    protected function updateOldDocument(\FPDF $doc): void
    {
        $doc->SetTitle('Title æ', true);
        $doc->SetSubject('Subject');
        $doc->SetAuthor('Author');
        $doc->SetCreator('Creator');
        $doc->SetKeywords('Keys words');
    }
}
