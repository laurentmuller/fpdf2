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

namespace fpdf\Tests\Fixture;

use fpdf\PdfDocument;
use fpdf\Traits\PdfAttachmentTrait;

class PdfDocumentAttachment extends PdfDocument
{
    use PdfAttachmentTrait;
}
