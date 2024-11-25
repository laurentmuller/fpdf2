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

namespace fpdf\Tests;

class PdfDocMarginTest extends AbstractPdfDocTestCase
{
    public function testBottomMargin(): void
    {
        $doc = $this->createDocument();
        $expected = $doc->getBottomMargin();
        $doc->setAutoPageBreak(true, $expected);
        self::assertSame($expected, $doc->getBottomMargin());
    }

    public function testRightMargin(): void
    {
        $doc = $this->createDocument();
        self::assertEqualsWithDelta(10.0, $doc->getRightMargin(), 0.01);
        $doc->setRightMargin(45.0);
        self::assertEqualsWithDelta(45.0, $doc->getRightMargin(), 0.01);
    }

    public function testTopMargin(): void
    {
        $doc = $this->createDocument();
        self::assertEqualsWithDelta(10.0, $doc->getTopMargin(), 0.01);
        $doc->setTopMargin(20.0);
        self::assertSame(20.0, $doc->getTopMargin());
    }

    public function testUseMargin(): void
    {
        $doc = $this->createDocument();
        $oldMargin = $doc->getCellMargin();
        $doc->useCellMargin(function () use ($doc): void {
            self::assertSame(0.0, $doc->getCellMargin());
        });
        $newMargin = $doc->getCellMargin();
        self::assertSame($oldMargin, $newMargin);
    }
}
