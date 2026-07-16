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

namespace fpdf\Enums;

/**
 * The {@link PDFWriter} state enumeration.
 */
enum PdfState
{
    /** The document is closed. */
    case CLOSED;
    /** The end page has been called. */
    case END_PAGE;
    /** The document has no page (not started). */
    case NO_PAGE;
    /** The start page has been called. */
    case PAGE_STARTED;

    /**
     * Returns a value indicating if the transition from this state to the new given state is allowed.
     *
     * @param PdfState $newState the new state to validate
     *
     * @return bool true if alllowed; false otherwise
     */
    public function isAllowed(self $newState): bool
    {
        $allowed = match ($this) {
            PdfState::NO_PAGE => [PdfState::NO_PAGE, PdfState::PAGE_STARTED, PdfState::CLOSED],
            PdfState::PAGE_STARTED => [PdfState::END_PAGE],
            PdfState::END_PAGE => [PdfState::END_PAGE, PdfState::PAGE_STARTED, PdfState::CLOSED],
            PdfState::CLOSED => [PdfState::CLOSED],
        };

        return \in_array($newState, $allowed, true);
    }
}
