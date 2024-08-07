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

namespace fpdf\Interfaces;

/**
 * Enumeration implementing this interface deals with a default value.
 *
 * @template T of \UnitEnum&PdfEnumDefaultInterface
 */
interface PdfEnumDefaultInterface
{
    /**
     * The default attribute name.
     */
    public const NAME = 'default';

    /**
     * Gets the default enumeration.
     *
     * @psalm-return T
     *
     * @throws \LogicException if default enumeration is not found
     */
    public static function getDefault(): self;

    /**
     * Returns if this enumeration is the default value.
     */
    public function isDefault(): bool;
}
