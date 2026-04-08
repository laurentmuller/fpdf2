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
     * Format this number to use for output.
     */
    public function formatNumber(): string
    {
        return \sprintf('%d 0 R', $this->number);
    }

    /**
     * @template TKey of array-key
     * @template TValue
     *
     * @param array<TKey, TValue>|null $array
     *
     * @phpstan-assert-if-true non-empty-array<TKey, TValue> $array
     */
    protected function isArray(?array $array): bool
    {
        return null !== $array && [] !== $array;
    }

    /**
     * @phpstan-assert-if-true non-empty-string $value
     */
    protected function isString(?string $value): bool
    {
        return null !== $value && '' !== $value;
    }
}
