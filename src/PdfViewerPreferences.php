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

namespace fpdf;

use fpdf\Enums\PdfDirection;
use fpdf\Enums\PdfDuplex;
use fpdf\Enums\PdfNonFullScreenPageMode;
use fpdf\Enums\PdfScaling;
use fpdf\Enums\PdfVersion;
use fpdf\Interfaces\PdfEnumDefaultInterface;

/**
 * Contains document preferences.
 */
class PdfViewerPreferences
{
    private bool $centerWindow = false;
    private PdfDirection $direction;
    private bool $displayDocTitle = false;
    private PdfDuplex $duplex;
    private bool $fitWindow = false;
    private bool $hideMenubar = false;
    private bool $hideToolbar = false;
    private bool $hideWindowUI = false;
    private PdfNonFullScreenPageMode $nonFullScreenPageMode;
    private bool $pickTrayByPDFSize = false;
    private PdfScaling $scaling;

    public function __construct()
    {
        $this->duplex = PdfDuplex::getDefault();
        $this->scaling = PdfScaling::getDefault();
        $this->direction = PdfDirection::getDefault();
        $this->nonFullScreenPageMode = PdfNonFullScreenPageMode::getDefault();
    }

    /**
     * Gets the predominant reading order for text.
     *
     * The default value is <code>PdfDirection::L2R</code>.
     */
    public function getDirection(): PdfDirection
    {
        return $this->direction;
    }

    /**
     * Gets the paper handling option that shall be used when printing the file from the print dialog.
     *
     * The default value is <code>PdfDuplex::NONE</code>.
     */
    public function getDuplex(): PdfDuplex
    {
        return $this->duplex;
    }

    /**
     * Gets the document’s page mode, specifying how to display the document on exiting full-screen mode.
     *
     * The default value is <code>PdfNonFullScreenPageMode::USE_NONE</code>.
     */
    public function getNonFullScreenPageMode(): PdfNonFullScreenPageMode
    {
        return $this->nonFullScreenPageMode;
    }

    /**
     * Gets the output string to put to the document's catalog.
     *
     * @return string the output string or empty ("") if all values are set to default
     */
    public function getOutput(): string
    {
        if (!$this->isChanged()) {
            return '';
        }

        $output = '/ViewerPreferences<<';
        $output .= $this->getOutputBool($this->fitWindow, 'FitWindow');
        $output .= $this->getOutputBool($this->hideToolbar, 'HideToolbar');
        $output .= $this->getOutputBool($this->hideMenubar, 'HideMenubar');
        $output .= $this->getOutputBool($this->hideWindowUI, 'HideWindowUI');
        $output .= $this->getOutputBool($this->centerWindow, 'CenterWindow');
        $output .= $this->getOutputBool($this->displayDocTitle, 'DisplayDocTitle');
        $output .= $this->getOutputBool($this->pickTrayByPDFSize, 'PickTrayByPDFSize');

        $output .= $this->getOutputEnum($this->duplex, 'Duplex');
        $output .= $this->getOutputEnum($this->direction, 'Direction');
        $output .= $this->getOutputEnum($this->scaling, 'PrintScaling');
        $output .= $this->getOutputEnum($this->nonFullScreenPageMode, 'NonFullScreenPageMode');

        return $output . '>>';
    }

    /**
     * Gets needed PDF version.
     */
    public function getPdfVersion(): PdfVersion
    {
        if ($this->pickTrayByPDFSize || $this->duplex !== PdfDuplex::getDefault()) {
            return PdfVersion::VERSION_1_7;
        }
        if ($this->scaling !== PdfScaling::getDefault()) {
            return PdfVersion::VERSION_1_6;
        }
        if ($this->displayDocTitle) {
            return PdfVersion::VERSION_1_4;
        }

        return PdfVersion::VERSION_1_3;
    }

    /**
     * Gets the page scaling option that shall be selected when a print dialog is displayed for this document.
     *
     * The default value is <code>PdfScaling::NONE</code>.
     */
    public function getScaling(): PdfScaling
    {
        return $this->scaling;
    }

    /**
     * Gets a value specifying whether to position the document’s window in the center of the screen.
     *
     * The default value is <code>false</code>.
     */
    public function isCenterWindow(): bool
    {
        return $this->centerWindow;
    }

    /**
     * Gets a value indicating if one or more values have been changed from default.
     */
    public function isChanged(): bool
    {
        return $this->fitWindow
            || $this->hideMenubar
            || $this->hideToolbar
            || $this->hideWindowUI
            || $this->centerWindow
            || $this->displayDocTitle
            || $this->pickTrayByPDFSize
            || !$this->duplex->isDefault()
            || !$this->scaling->isDefault()
            || !$this->direction->isDefault()
            || !$this->nonFullScreenPageMode->isDefault();
    }

    /**
     * Gets a value specifying whether the window’s title bar should display the document title taken from the title
     * entry of the document information dictionary.
     *
     * If <code>false</code>, the title bar should instead display the name of the PDF file containing the document.
     *
     *
     * The default value is <code>false</code>.
     */
    public function isDisplayDocTitle(): bool
    {
        return $this->displayDocTitle;
    }

    /**
     * Gets a value specifying whether to resize the document’s window to fit the size of the first displayed page.
     *
     * The default value is <code>false</code>.
     */
    public function isFitWindow(): bool
    {
        return $this->fitWindow;
    }

    /**
     * Gets a value specifying whether to hide the conforming reader’s menu bar when the document is active.
     *
     * The default value is <code>false</code>.
     */
    public function isHideMenubar(): bool
    {
        return $this->hideMenubar;
    }

    /**
     * Gets a value specifying whether to hide the conforming reader’s toolbars when the document is active.
     *
     * The default value is <code>false</code>.
     */
    public function isHideToolbar(): bool
    {
        return $this->hideToolbar;
    }

    /**
     * Gets a value specifying whether to hide user interface elements in the document’s window (such as scroll bars
     * and navigation controls), leaving only the document’s contents displayed.
     *
     * The default value is <code>false</code>.
     */
    public function isHideWindowUI(): bool
    {
        return $this->hideWindowUI;
    }

    /**
     * Gets a value specifying whether the PDF page size shall be used to select the input paper tray. This setting
     * influences only the preset values used to populate the print dialog presented by a conforming reader.
     *
     * The default value is <code>false</code>.
     */
    public function isPickTrayByPDFSize(): bool
    {
        return $this->pickTrayByPDFSize;
    }

    /**
     * Resets all values to default.
     */
    public function reset(): self
    {
        $this->fitWindow = false;
        $this->hideToolbar = false;
        $this->hideMenubar = false;
        $this->hideWindowUI = false;
        $this->centerWindow = false;
        $this->displayDocTitle = false;
        $this->pickTrayByPDFSize = false;

        $this->duplex = PdfDuplex::getDefault();
        $this->scaling = PdfScaling::getDefault();
        $this->direction = PdfDirection::getDefault();
        $this->nonFullScreenPageMode = PdfNonFullScreenPageMode::getDefault();

        return $this;
    }

    /**
     * Sets a value specifying whether to position the document’s window in the center of the screen.
     */
    public function setCenterWindow(bool $centerWindow = true): self
    {
        $this->centerWindow = $centerWindow;

        return $this;
    }

    /**
     * Sets the predominant reading order for text.
     */
    public function setDirection(PdfDirection $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Sets a value specifying whether the window’s title bar should display the document title taken from the title
     * entry of the document information dictionary.
     *
     * If <code>false</code>, the title bar should instead display the name of the PDF file containing the document.
     */
    public function setDisplayDocTitle(bool $displayDocTitle = true): self
    {
        $this->displayDocTitle = $displayDocTitle;

        return $this;
    }

    /**
     * Sets paper handling option that shall be used when printing the file from the print dialog.
     */
    public function setDuplex(PdfDuplex $duplex): self
    {
        $this->duplex = $duplex;

        return $this;
    }

    /**
     * Sets a value specifying whether to resize the document’s window to fit the size of the first displayed page.
     */
    public function setFitWindow(bool $fitWindow = true): self
    {
        $this->fitWindow = $fitWindow;

        return $this;
    }

    /**
     * Sets a value specifying whether to hide the conforming reader’s menu bar when the document is active.
     */
    public function setHideMenubar(bool $hideMenubar = true): self
    {
        $this->hideMenubar = $hideMenubar;

        return $this;
    }

    /**
     * Sets a value specifying whether to hide the conforming reader’s toolbars when the document is active.
     */
    public function setHideToolbar(bool $hideToolbar = true): self
    {
        $this->hideToolbar = $hideToolbar;

        return $this;
    }

    /**
     * Sets a value specifying whether to hide user interface elements in the document’s window (such as scroll bars
     * and navigation controls), leaving only the document’s contents displayed.
     */
    public function setHideWindowUI(bool $hideWindowUI = true): self
    {
        $this->hideWindowUI = $hideWindowUI;

        return $this;
    }

    /**
     * Sets the document’s page mode, specifying how to display the document on exiting full-screen mode.
     */
    public function setNonFullScreenPageMode(PdfNonFullScreenPageMode $nonFullScreenPageMode): self
    {
        $this->nonFullScreenPageMode = $nonFullScreenPageMode;

        return $this;
    }

    /**
     * Sets a value specifying whether the PDF page size shall be used to select the input paper tray. This setting
     * influences only the preset values used to populate the print dialog presented by a conforming reader.
     */
    public function setPickTrayByPDFSize(bool $pickTrayByPDFSize = true): self
    {
        $this->pickTrayByPDFSize = $pickTrayByPDFSize;

        return $this;
    }

    /**
     * Sets page scaling option that shall be selected when a print dialog is displayed for this document.
     */
    public function setScaling(PdfScaling $scaling): self
    {
        $this->scaling = $scaling;

        return $this;
    }

    private function getOutputBool(bool $value, string $key): string
    {
        return $value ? \sprintf('/%s true', $key) : '';
    }

    /**
     * @phpstan-template T of \BackedEnum&PdfEnumDefaultInterface
     *
     * @phpstan-param (T&PdfEnumDefaultInterface<T>) $enum
     */
    private function getOutputEnum(\BackedEnum&PdfEnumDefaultInterface $enum, string $key): string
    {
        return $enum->isDefault() ? '' : \sprintf('/%s %s', $key, $enum->value);
    }
}
