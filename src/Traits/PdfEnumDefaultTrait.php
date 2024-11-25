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

namespace fpdf\Traits;

use Elao\Enum\ExtrasTrait;
use fpdf\Interfaces\PdfEnumDefaultInterface;

/**
 * Trait for enumeration implementing <code>PdfEnumDefaultInterface</code> interface.
 *
 * @phpstan-require-implements PdfEnumDefaultInterface
 */
trait PdfEnumDefaultTrait
{
    use ExtrasTrait;

    /**
     * Gets the default case enumeration.
     *
     * @throws \LogicException if no default case enumeration is found
     */
    public static function getDefault(): self
    {
        foreach (static::cases() as $value) {
            if ($value->isDefault()) {
                return $value;
            }
        }
        throw new \LogicException(\sprintf('No default value found for "%s" enumeration.', __CLASS__));
    }

    public function isDefault(): bool
    {
        return (bool) $this->getExtra(PdfEnumDefaultInterface::NAME);
    }
}
