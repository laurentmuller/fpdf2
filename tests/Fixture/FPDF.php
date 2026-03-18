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

/**
 * Override base class for testing purpose.
 */
class FPDF extends \FPDF
{
    #[\Override]
    protected function _out(mixed $s): void
    {
        parent::_out($this->trimEndSpace($s));
    }

    #[\Override]
    protected function _put(mixed $s): void
    {
        parent::_put($this->trimEndSpace($s));
    }

    private function trimEndSpace(mixed $s): mixed
    {
        if (!\is_string($s) || !\str_ends_with($s, ' ]')) {
            return $s;
        }

        return \substr($s, 0, -2) . ']';
    }
}
