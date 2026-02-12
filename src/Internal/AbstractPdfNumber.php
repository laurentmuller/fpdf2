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
 * Abstract class for object number.
 *
 * @internal
 */
abstract class AbstractPdfNumber
{
    /** The object number. */
    public int $number = 0;

    /**
     * @phpstan-assert-if-true non-empty-string $value
     */
    protected function isString(?string $value): bool
    {
        return null !== $value && '' !== $value;
    }
}
