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

class PdfSapiHeadersDocument extends PdfDocument
{
    public function __construct(private readonly bool $phpSapiCli = true, private readonly bool $headersSent = false)
    {
        parent::__construct();
    }

    #[\Override]
    protected function isHeadersSent(?string &$filename = null, ?int &$line = null): bool
    {
        // for code coverage
        parent::isHeadersSent($filename, $line);
        $filename = 'code.php';
        $line = 10;

        return $this->headersSent;
    }

    #[\Override]
    protected function isPhpSapiCli(): bool
    {
        return $this->phpSapiCli;
    }
}
