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

namespace fpdf\Internal;

/**
 * Contains information about an attached file.
 *
 * @internal
 */
final class PdfAttachment extends AbstractPdfNumber
{
    /**
     * @param string $file        the file path
     * @param string $name        the file name
     * @param string $description the file description
     */
    public function __construct(
        public readonly string $file,
        public readonly string $name,
        public readonly string $description,
    ) {}

    /**
     * @phpstan-assert-if-true non-empty-string $this->description
     */
    public function isDescription(): bool
    {
        return $this->isString($this->description);
    }
}
