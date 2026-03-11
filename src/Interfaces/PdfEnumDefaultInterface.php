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

namespace fpdf\Interfaces;

/**
 * Enumeration implementing this interface deals with a default value.
 *
 * @template T of PdfEnumDefaultInterface&\UnitEnum
 */
interface PdfEnumDefaultInterface
{
    /** The default attribute name. */
    public const string NAME = 'default';

    /**
     * Gets the default enumeration.
     *
     * @phpstan-return T
     *
     * @throws \LogicException if the default enumeration is not found
     */
    public static function getDefault(): self;

    /**
     * Returns if this enumeration is the default value.
     */
    public function isDefault(): bool;
}
