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

namespace Enums;

use fpdf\Enums\PdfState;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PdfStateTest extends TestCase
{
    /**
     * @phpstan-return \Generator<int, array{PdfState, PdfState, bool}>
     */
    public static function getValues(): \Generator
    {
        yield [PdfState::NO_PAGE, PdfState::NO_PAGE, true];
        yield [PdfState::NO_PAGE, PdfState::PAGE_STARTED, true];
        yield [PdfState::NO_PAGE, PdfState::END_PAGE, false];
        yield [PdfState::NO_PAGE, PdfState::CLOSED, true];

        yield [PdfState::PAGE_STARTED, PdfState::NO_PAGE, false];
        yield [PdfState::PAGE_STARTED, PdfState::PAGE_STARTED, false];
        yield [PdfState::PAGE_STARTED, PdfState::END_PAGE, true];
        yield [PdfState::PAGE_STARTED, PdfState::CLOSED, false];

        yield [PdfState::END_PAGE, PdfState::NO_PAGE, false];
        yield [PdfState::END_PAGE, PdfState::PAGE_STARTED, true];
        yield [PdfState::END_PAGE, PdfState::END_PAGE, true];
        yield [PdfState::END_PAGE, PdfState::CLOSED, true];

        yield [PdfState::CLOSED, PdfState::NO_PAGE, false];
        yield [PdfState::CLOSED, PdfState::PAGE_STARTED, false];
        yield [PdfState::CLOSED, PdfState::END_PAGE, false];
        yield [PdfState::CLOSED, PdfState::CLOSED, true];
    }

    #[DataProvider('getValues')]
    public function testIsAllowed(PdfState $state, PdfState $newState, bool $expected): void
    {
        $actual = $state->isAllowed($newState);
        self::assertSame($expected, $actual);
    }
}
